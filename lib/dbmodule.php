<?php
  require_once(dirname(__FILE__)."/commonTools.php");
  require_once(dirname(__FILE__)."/quickFormConfig.php");
class pdoConn
{
    public function getTablePrefix()
    {
        return "";
    }

    public function processTableObj($obj)
    {
        return $obj;
    }
    
    function pdoConn($hostname=DB_HOST,$dbname=DB_NAME,$username=DB_USERNAME,$password=DB_PASSWORD)
    {
       $dsn = "mysql:";
       $dsn .="host=".$hostname.";dbname=".$dbname;
       $conn = new PDO($dsn, $username, $password);
       return $conn;
    }

}
class SqlLite extends SQLite3
{
    var $errormsg;

    function execSql ($strsql)
    {
      if($this->debug)
      {
            echo "<br>";
            $time = microtime(true);
            echo $strsql;
       }
       $ret =  $this->exec($strsql);
       if($this->debug)
       {
          echo " Run time:".(microtime(true)-$time)/1000;
       } 
       return $ret;  
    }
    function openQuery($strsql)
    {
          if($this->debug)
          {
            echo "<br>";
            $time = microtime(true);
            echo $strsql;
          }
         $ret = $this->query($strsql);
         if($this->debug)
         {
            echo " Run time:".(microtime(true)-$time)/1000;
         }
         return $ret; 
    }
    public function getTablePrefix()
    {
        return "";
    }
    
    public function processTableObj($obj)
    {
        return $obj;
    }

    public function getSqlMode()
    {
        return false;
    }

    function execTransaction ($p_query)
    {
       
        $this->errormsg = '';
        if (!is_array($p_query)) {
            $this->errormsg = "Not a valid transaction sql.\n";
            return false;
        }
       $result = false;
        if ($this->execSql('BEGIN TRANSACTION'))
        {
            foreach ( $p_query as $query)
            {
                $time = null;
                if($this->debug)
                {
                    echo "<br>";
                    $time = microtime(true);
                    echo $query;
                }
                $result = $this->execSql($query);
                if($this->debug)
                {
                    echo " Run time:".(microtime(true)-$time)/1000;
                }
                if (!$result) {
                    $this->errormsg = $this->lastErrorCode() . ": " . $this->lastErrorMsg() . "\n";
                    if($this->debug)
                    {
                        echo  $this->lastErrorCode()  . ": " . $this->lastErrorMsg(); 
                    }
                    break;
                }
            }

            if ($result)
                $result= $this->execSql('COMMIT', $this->link);
            else
                $this->execSql('ROLLBACK', $this->link);
        }
        
        return $result;
    }
    
}
class Database
{
    
	var $hostname ;
	var $dbname   ;
	var $username ;
	var $password ;

	var $link;
	var $result;
	var $row;
	var $debug;
	var $rownum;
	var $fieldnum;
	var $errormsg;
    var $pdoConn = null;
    
    public function processTableObj($obj)
    {
        return "`".$obj."`";
    }
   
    public function getResult()
    {
        return $this->result;
    }
    public function getLink()
    {
        return $this->link;
    }
    public function setDebug($debug)
    {
        $this->debug = $debug;
    }
    public function getPdo()
    {
        if($this->pdoConn==null)
        {        
            $dsn = "mysql:host=".$this->hostname.";dbname=".$this->dbname;
            $this->pdoConn = new PDO($dsn, $this->username , $this->password);
        }
        return $this->pdoConn;
    }
    
    public function getSqlMode()
    {
        return false;
    }
    
	function Database($hostname=DB_HOST,$dbname=DB_NAME,$username=DB_USERNAME,$password=DB_PASSWORD,$needEcho=false)
	{
		$this->hostname = $hostname;
		$this->dbname   = $dbname;
		$this->username = $username;
		$this->password = $password;
		$this->debug    = false;
		$this->errormsg = '';
		
		//connect the database
		/*if($this->debug) 
			printf ("hostname:%s <br> dbname:%s <br> username:%s <br>  password:%s ",$this->hostname,$this->dbname,$this->username,$this->password);
		if($this->debug)
		{
			echo "<br>";
			echo $strsql;
		}*/
       // echo $this->hostname." ".$this->username." ".$this->password;
		$this->link = mysql_connect($this->hostname , $this->username, $this->password);//, 1, 131072); //CLIENT_MULTI_RESULTS
		if (!$this->link )
		{
			printf("Connect failed: %s\n", mysql_error());
			return FALSE;
		}
		//select database		
		if (! ($this->selectDb($this->dbname)))
        {
            echo "Can't connect to ".$dbname;
             return FALSE;
        }
        if($needEcho)
        {
		  echo "Connected Successfully";
        }
		return TRUE;
	}

	//select database
	function selectDb ($dbname_in)
	{
		$this->errormsg = '';	
		//echo "<br>db pass 1";
		$db_selected = mysql_select_db($dbname_in, $this->link);
		if(!$db_selected )
		{
			printf("Select Database %s Error: %s\n", $dbname_in, mysql_error($this->link));
			return FALSE;
		}
		return TRUE;
  }
  
  //free the result return by a sql query, and disconnect the db
	function disconnect()
	{
		$this->errormsg = '';
		if($this->link)
			@mysql_close ($this->link);
	}

	//execute a sql query with return data needed
	// connect db, return data, 
	// dont disconnect, should disconnect the db explicitly by the caller
	function openQuery ($strsql)
	{
    
        $strsql = StringTools::conv($strsql,QuickFormConfig::$encode);
		$this->errormsg = '';
		$this->rownum = 0;
        $time = null;
		//echo "<br>db pass 2";
		if($this->debug)
		{
			echo "<br>";
            $time = microtime(true);
			echo $strsql;
		}
        if(QuickFormConfig::$dbEncode!=null&&trim(QuickFormConfig::$dbEncode)!="")
        {

            $encodeSql = "set names '".QuickFormConfig::$dbEncode."'";
            mysql_query($encodeSql, $this->link);
        }
		if($this->result = mysql_query($strsql, $this->link))
		{
			//echo "<br>db pass 3";
            if($this->debug)
            {
                echo " Run time:".(microtime(true)-$time)/1000;
            }
			$this->rownum = mysql_num_rows($this->result);
			$this->fieldnum = mysql_num_fields($this->result);
            
			return TRUE;
		}
		$this->errormsg = mysql_errno($this->link) . ": " . mysql_error($this->link) . "\n";
		return FALSE;
	}

	//return the next row of the return data
	//if no more data, free result
	function nextResult()
	{
		$this->errormsg = '';
		if($this->row = mysql_fetch_row($this->result))
		{
			return TRUE;
		}
		else //no more data
		{
			if (is_resource($this->result))
				@mysql_free_result ($this->result);
			return FALSE;
		}
	}

	//execute a sql statement without return data needed
	function execSql ($strsql)
	{   
		$this->errormsg = '';
		$time = null;
		if($this->debug)
		{
			echo "<br>";
            $time = microtime(true);
			echo $strsql;
		}
	
		if($this->result = mysql_query($strsql, $this->link))
		{
            if($this->debug)
            {
                echo "Run time:".(microtime(true)-$time)/1000;
            }
			//free result
			if (is_resource($this->result))
				@mysql_free_result ($this->result);
			return TRUE;
		}
		$this->errormsg = mysql_errno($this->link) . ": " . mysql_error($this->link) . "\n";
		return FALSE;
	}

	//excute a transaction. all sqls should be stored into 
	// an array, if any sql fails, roolback and return false; if commit fails,
	// return false; if all sqls success, and commit success, return true
	function execTransaction ($p_query)
	{
       
		$this->errormsg = '';
		if (!is_array($p_query)) {
			$this->errormsg = "Not a valid transaction sql.\n";
			return false;
		}
       $result = false;
		if (mysql_query('START TRANSACTION', $this->link))
		{
			foreach ( $p_query as $query)
			{
                $time = null;
                if($this->debug)
                {
                    echo "<br>";
                    $time = microtime(true);
                    echo $query;
                }
				$result = mysql_query($query, $this->link);
                if($this->debug)
                {
                    echo " Run time:".(microtime(true)-$time)/1000;
                }
				if (!$result) {
            		$this->errormsg = mysql_errno($this->link) . ": " . mysql_error($this->link) . "\n";
                    if($this->debug)
                    {
				    	echo  mysql_errno($this->link) . ": " . mysql_error($this->link); 
                    }
                    break;
				}
			}

			if ($result)
				$result= mysql_query('COMMIT', $this->link);
			else
				mysql_query('ROLLBACK', $this->link);
		}
		
		return $result;
	}
	
	function errorInfo ()
	{
		return $this->errormsg;
	}
	function startTransaction()
	{
		$result = false;
		
		//if (mysql_query('START TRANSACTION', $this->link))
		if (mysql_query("START TRANSACTION") )
		{
			$result = true;
		}
		else
		{
			$this->errormsg = mysql_errno($this->link) . ": " . mysql_error($this->link) . "\n";
			$result = false;
		}
		
		return $result;
	}
	function commitTransaction()
	{
		$result = false;
		
		//if( mysql_query('COMMIT', $this->link) )
		if( mysql_query("COMMIT") )
		{
			$result = true;
			mysql_query("END");
		}
		else
		{
			$this->errormsg = mysql_errno($this->link) . ": " . mysql_error($this->link) . "\n";
		}

		return $result;
	}
	function rollbackTransaction()
	{
		$result = false;

		//if( mysql_query('ROLLBACK', $this->link) )
		if( mysql_query("ROLLBACK") )
		{
			$result = true;
			mysql_query("END");
		}
		else
		{
			$this->errormsg = mysql_errno($this->link) . ": " . mysql_error($this->link) . "\n";
		}

		return $result;
	}
}


class SqlServer extends Mssql
{
     var $isSqlSrv = true;
   
    public function getPdo()
    {
        if($this->pdoConn==null)
        {        
            $dsn = "sqlsrv:Server=".$this->hostname.";Database=".$this->dbname;
            $this->pdoConn = new PDO($dsn, $this->username , $this->password);
        }
        return $this->pdoConn;
    }
    function SqlServer($hostname=DB_HOST,$dbname=DB_NAME,$username=DB_USERNAME,$password=DB_PASSWORD)
    {

        $this->hostname = $hostname;
        $this->dbname   = $dbname;
        $this->username = $username;
        $this->password = $password;
        $this->debug    = false;
        $this->errormsg = '';
      
        $connectionInfo = array( "Database"=>$this->dbname,"UID"=>$this->username, "PWD"=> $this->password);
        $this->link = sqlsrv_connect( $this->hostname,$connectionInfo);
        if( $this->link === false ) {
        die( print_r( sqlsrv_errors(), true));
}
    
        return TRUE;
    }

    function disconnect()
    {
        $this->errormsg = '';
        if($this->link)
            @sqlsrv_close($this->link);
    }

     function openQuery ($strsql)
    {
        $strsql = StringTools::conv($strsql,QuickFormConfig::$encode);
        $this->errormsg = '';
        $this->rownum = 0;
        //echo "<br>db pass 2";
        if($this->debug)
        {
            echo "<br>";
            echo $strsql;
        }
        if($this->result = sqlsrv_query($this->link,$strsql))
        {
            //echo "<br>db pass 3";
            $this->rownum = sqlsrv_num_rows($this->result);
            $this->fieldnum = sqlsrv_num_fields($this->result);
            return TRUE;
        }
        $this->errormsg = "";
        return FALSE;
    }

    function nextResult()
    {
        $this->errormsg = '';
        if($this->row = sqlsrv_fetch_array($this->result))
        {
            return TRUE;
        }
        else //no more data
        {
            return FALSE;
        }
    }

    //execute a sql statement without return da$this->link,ta need
    function execSql ($strsql)
    {
        $this->errormsg = '';
        if($this->debug)
        {
            echo "<br>";
            echo $strsql;
        }
    
        if($this->result = sqlsrv_query($this->link,$strsql))
        {
            //free result
           
            return TRUE;
        }
        $this->errormsg = "";
        return FALSE;
    }

    //excute a transaction. all sqls should be stored into 
    // an array, if any sql fails, roolback and return false; if commit fails,
    // return false; if all sqls success, and commit success, return true
    function execTransaction ($p_query)
    {

        $this->errormsg = '';
        if (!is_array($p_query)) {
            $this->errormsg = "Not a valid transaction sql.\n";
            return false;
        }
       $result = false;
        if (sqlsrv_begin_transaction($this->link))
        {
            
            foreach ( $p_query as $query)
            {

                $result = sqlsrv_query( $this->link,$query);
                if (!$result) {
                    $this->errormsg ="";
                    break;
                }
            }

            if ($result)
            {
               
                $result= sqlsrv_commit($this->link);
            }
            else
            {

                sqlsrv_rollback($this->link);
            }
        }
       
        return $result;
    }

}

class Mssql
{

    var $hostname ;
    var $dbname   ;
    var $username ;
    var $password ;
    var $isSqlSrv = false;
    var $link;
    var $result;
    var $row;
    var $debug;
    var $rownum;
    var $fieldnum;
    var $errormsg;
    var $pdoConn = null;
    public function getResult()
    {
        return $this->result;
    }
    public function processTableObj($obj)
    {
        return "[".$obj."]";
    }
   
    public function getLink()
    {
        return $this->link;
    }
    public function getPdo()
    {
        if($this->pdoConn==null)
        {        
            $dsn = "dblib:host=".$this->hostname.";dbname=".$this->dbname;
            $this->pdoConn = new PDO($dsn, $this->username , $this->password);
        }
        return $this->pdoConn;
    }
    
    
    function Mssql($hostname,$dbname,$username,$password)
    {

        $this->hostname = $hostname;
        $this->dbname   = $dbname;
        $this->username = $username;
        $this->password = $password;
        $this->debug    = false;
        $this->errormsg = '';
      
        //connect the database
        /*if($this->debug) 
            printf ("hostname:%s <br> dbname:%s <br> username:%s <br>  password:%s ",$this->hostname,$this->dbname,$this->username,$this->password);
        if($this->debug)
        {
            echo "<br>";
            echo $strsql;
        }*/

        $this->link = mssql_connect($this->hostname , $this->username, $this->password);//, 1, 131072); //CLIENT_MULTI_RESULTS
        if (!$this->link)
        {
            printf("Connect failed: %s\n");
            return FALSE;
        }

    
        
        //select database       
        if (! ($this->selectDb($this->dbname))) return FALSE;
        
        return TRUE;
    }

        public function getSqlMode()
        {
            return true;
        }

    //select database
    function selectDb ($dbname_in)
    {
        $this->errormsg = '';   
        //echo "<br>db pass 1";
        $db_selected = mssql_select_db($dbname_in, $this->link);
        if(!$db_selected )
        {
            printf("Select Database %s Error: %s\n", $dbname_in);
            return FALSE;
        }
        return TRUE;
  }
    function isSqlSrv()
    {
        return $this->isSqlSrv;
    }
  //free the result return by a sql query, and disconnect the db
    function disconnect()
    {
        $this->errormsg = '';
        if($this->link)
            @mssql_close ($this->link);
    }

    //execute a sql query with return data needed
    // connect db, return data, 
    // dont disconnect, should disconnect the db explicitly by the caller
    function openQuery ($strsql)
    {
        $strsql = StringTools::conv($strsql,QuickFormConfig::$encode);
        $this->errormsg = '';
        $this->rownum = 0;
        
        //echo "<br>db pass 2";
        if($this->debug)
        {
            echo "<br>";
            echo $strsql;
        }
        if($this->result = mssql_query($strsql, $this->link))
        {
            //echo "<br>db pass 3";
            $this->rownum = mssql_num_rows($this->result);
            $this->fieldnum = mssql_num_fields($this->result);
            return TRUE;
        }
        $this->errormsg = "";
        return FALSE;
    }

    //return the next row of the return data
    //if no more data, free result
    function nextResult()
    {
        $this->errormsg = '';
        if($this->row = mssql_fetch_row($this->result))
        {
            return TRUE;
        }
        else //no more data
        {
            if (is_resource($this->result))
                @mssql_free_result ($this->result);
            return FALSE;
        }
    }

    //execute a sql statement without return data needed
    function execSql ($strsql)
    {
        $this->errormsg = '';
     
        if($this->debug)
        {
            echo "<br>";
            echo $strsql;
        }
    
        if($this->result = mssql_query($strsql, $this->link))
        {
            //free result
            if (is_resource($this->result))
                @mssql_free_result ($this->result);
            return TRUE;
        }
        $this->errormsg = "";
        return FALSE;
    }

    //excute a transaction. all sqls should be stored into 
    // an array, if any sql fails, roolback and return false; if commit fails,
    // return false; if all sqls success, and commit success, return true
    function execTransaction ($p_query)
    {

        $this->errormsg = '';
        if (!is_array($p_query)) {
            $this->errormsg = "Not a valid transaction sql.\n";
            return false;
        }
        $result = false;
        if (mssql_query('BEGIN TRANSACTION', $this->link))
        {
            foreach ( $p_query as $query)
            {

                $result = mssql_query($query, $this->link);
                if (!$result) {
                    $this->errormsg ="";
                    break;
                }
            }

            if ($result)
                $result= mssql_query('COMMIT TRANSACTION', $this->link);
            else
                mssql_query('ROLLBACK TRANSACTION', $this->link);
        }
        
        return $result;
    }
    
    function errorInfo ()
    {
        return $this->errormsg;
    }
}



?>