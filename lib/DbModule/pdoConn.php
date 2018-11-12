<?php
/**
 * Created by PhpStorm.
 * User: Zack
 * Date: 11/9/2018
 * Time: 8:18 PM
 */

namespace Quickplus\Lib\DbModule;

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