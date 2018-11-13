<?php
/**
 * Created by PhpStorm.
 * User: Zack
 * Date: 11/9/2018
 * Time: 8:20 PM
 */

namespace Quickplus\Lib\DbModule;


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

    public function isMssql()
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