<?php
/**
 * Created by PhpStorm.
 * User: Zack
 * Date: 11/9/2018
 * Time: 8:20 PM
 */

namespace Quickplus\Lib\DbModule;

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