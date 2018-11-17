<?php
/**
 * Created by PhpStorm.
 * User: Zack
 * Date: 11/9/2018
 * Time: 8:19 PM
 */

namespace Quickplus\Lib\DbModule;
use \PDO;
use Quickplus\Lib\Tools\StringTools as StringTools;
use Quickplus\Lib\QuickFormConfig as QuickFormConfig;
use Quickplus\Lib\parameters;
class Database extends PDO
{
    protected  $hostname ;
    protected $dbname   ;
    protected $username ;
    protected $password ;
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

    public function getLastInsertRowID()
    {
        return  $this -> lastinsertid();
    }

    public function getResult()
    {
        return $this->result;
    }

    public function setDebug($debug)
    {
        $this->debug = $debug;
    }
    public function isMssql()
    {
        return false;
    }
    protected function getDsn($dbname,$hostname)
    {

        $dsn = 'mysql:dbname='.$dbname.';host='.$hostname;
        return $dsn;
    }

    function __construct($hostname=parameters::DB_HOST,$dbname=parameters::DB_NAME,$username=parameters::DB_USERNAME,$password=parameters::DB_PASSWORD,$needEcho=false)
    {
        $this->hostname = $hostname;
        $this->dbname   = $dbname;
        $this->username = $username;
        $this->password = $password;
        $this->debug    = false;
        $this->errormsg = '';

        try
        {
            parent::__construct($this->getDsn($this->dbname,$this->hostname),$this->username,$this->password);
        }
        catch (PDOException $e) {
            echo 'Connection failed: ' . $e->getMessage();
            return FALSE;
        }
        if($needEcho)
        {
            echo "Connected Successfully";
        }
        return TRUE;
    }


    public function selectDb ($dbname_in)
    {
        try
        {
            parent::__construct($this->getDsn($dbname_in,$this->hostname),$this->username,$this->password);
        }
        catch (PDOException $e) {
            echo 'Connection failed: ' . $e->getMessage();
            return FALSE;
        }
        if($needEcho)
        {
            echo "Connected Successfully";
        }
        return TRUE;
    }

    public  function openQuery ($strsql)
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
            $this->exec($encodeSql);
        }
        $stmt = null;
        if($stmt = $this->query($strsql))
        {
            //echo "<br>db pass 3";
            if($this->debug)
            {
                echo " Run time:".(microtime(true)-$time)/1000;
            }
            $stmt->setFetchMode(PDO::FETCH_ASSOC);
            $this->result = $stmt->fetchAll();
            $this->rownum = count( $this->result);
            $this->fieldnum = $stmt->columnCount();;
            return TRUE;
        }
        $this->errormsg = $stmt->errorCode() . ": " . $stmt->errorInfo() . "\n";
        return FALSE;
    }

    public function execSql ($strsql)
    {
        $this->errormsg = '';
        $time = null;
        if($this->debug)
        {
            echo "<br>";
            $time = microtime(true);
            echo $strsql;
        }
        $stmt = null;
        if($stmt = $this->exec($strsql))
        {
            if($this->debug)
            {
                echo "Run time:".(microtime(true)-$time)/1000;
            }
            $this->result  =null;
            return TRUE;
        }
        $this->errormsg = $stmt->errorCode() . ": " . $stmt->errorInfo() . "\n";
        return FALSE;
    }

    public function execTransaction($p_query)
    {

        $this->errormsg = '';
        if (!is_array($p_query)) {
            $this->errormsg = "Not a valid transaction sql.\n";
            return false;
        }
        $result = true;
        if ($this->startTransaction())
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
                $stmt = $this->exec($query);
                if($this->debug)
                {
                    echo " Run time:".(microtime(true)-$time)/1000;
                }
                if (!$stmt) {
                    $result =false;
                    $this->errormsg =$stmt->errorCode() . ": " . $stmt->errorInfo() . "\n";
                    if($this->debug)
                    {
                        echo  $stmt->errorCode() . ": " . $stmt->errorInfo()."\n";
                    }
                    break;
                }
            }

            if ($result)
            {
                $result= $this->commitTransaction();
            }
            else
            {
                $result= $this->rollbackTransaction();
            }
        }

        return $result;
    }

    public function errorInfo ()
    {
        return $this->errormsg;
    }

    public function startTransaction()
    {
        $result = $this->beginTransaction();
        return $result;
    }
    public function commitTransaction()
    {
        $result = $this->commit();
        return $result;
    }
    public function rollbackTransaction()
    {
        $result = $this->rollBack();
        return $result;
    }



}