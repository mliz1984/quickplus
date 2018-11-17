<?php
/**
 * Created by PhpStorm.
 * User: Zack
 * Date: 11/9/2018
 * Time: 8:18 PM
 */

namespace Quickplus\Lib\DbModule;
use Quickplus\Lib\parameters;
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

    function pdoConn($hostname=parameters::DB_HOST,$dbname=parameters::DB_NAME,$username=parameters::DB_USERNAME,$password=parameters::DB_PASSWORD)
    {
        $dsn = "mysql:";
        $dsn .="host=".$hostname.";dbname=".$dbname;
        $conn = new PDO($dsn, $username, $password);
        return $conn;
    }

}