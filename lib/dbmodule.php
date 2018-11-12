<?php
  require_once(dirname(__FILE__)."/commonTools.php");
  require_once(dirname(__FILE__)."/quickFormConfig.php");

class Database
{
    
	protected $hostname ;
	protected $dbname   ;
	protected $username ;
	protected $password ;

	protected $link;
	protected $result;
	protected $row;
	protected $debug;
	protected $rownum;
	protected $fieldnum;
	protected $errormsg;
    
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
  
    
    public function getSqlMode()
    {
        return false;
    }
    
    function __construct($hostname=DB_HOST,$dbname=DB_NAME,$username=DB_USERNAME,$password=DB_PASSWORD,$debug=false)
	{
		$this->hostname = $hostname;
		$this->dbname   = $dbname;
		$this->username = $username;
		$this->password = $password;
		$this->debug    = false;
		$this->errormsg = '';
        $dsn = "mysql:host=".$this->hostname.";dbname=".$this->dbname;
        try {
        $this->link = new PDO($dsn, $this->username , $this->password);
            } catch (PDOException $e)
            {
			printf("Connect failed: %s\n", $this->link->errorInfo();
			return FALSE;
		}

        if($needEcho)
        {
		  echo "Connected Successfully";
        }
		return TRUE;
	}

	//select database
    public function selectDb ($dbname_in)
	{

        $dsn = "mysql:host=".$this->hostname.";dbname=".$dbname_in;
        try {
            $this->link = new PDO($dsn, $this->username , $this->password);
                } catch (PDOException $e)
                {
                printf("Connect failed: %s\n", $this->link->errorInfo();
                return FALSE;
            }
		return TRUE;
  }
  
  //free the result return by a sql query, and disconnect the db
	public function disconnect()
	{
		$this->errormsg = '';
		$this->link = null;
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

