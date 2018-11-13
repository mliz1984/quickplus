<?php
/**
 * Created by PhpStorm.
 * User: Zack
 * Date: 11/9/2018
 * Time: 8:19 PM
 */

namespace Quickplus\Lib\DbModule;


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

    public function isMssql()
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