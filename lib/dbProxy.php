<?php
namespace Quickplus\Lib;
use Quickplus\Lib\pdoConn as pdoConn;
use Quickplus\Lib\SqlLite as SqlLite;
use Quickplus\Lib\Database as Database;
use Quickplus\Lib\SqlServer as SqlServer;
use Quickplus\Lib\Mssql as Mssql;
use Quickplus\Lib\Parameters;
use Quickplus\Lib\QuickProxy;

  class DbProxy extends QuickProxy
  {
  	function __construct()
  	{
  		$this->setMethodMapping("DbProxy");
  	}
   protected function execSql($data)
   {
   		$db = $this->getDbConnection($data);
   		$sql = $data["sql"];
   		return $db->execSql($sql);
   }


   protected function getDbConnection($data)
   {
   		$dbtype = "DataBase";
   		if(isset($data["dbtype"])&&trim($data["dbtype"])!="")
   		{
   			$dbtype = $data["dbtype"];
   		}
   		$hostname=DB_HOST;
   		if(isset($data["hostname"])&&trim($data["hostname"])!="")
   		{
   			$hostname = $data["hostname"];
   		}
   		$dbname=DB_NAME;
   		if(isset($data["dbname"])&&trim($data["dbname"])!="")
   		{
   			$dbname = $data["dbname"];
   		}
   		$username=DB_USERNAME;
   		if(isset($data["username"])&&trim($data["username"])!="")
   		{
   			$username = $data["username"];
   		}
   		$password=DB_PASSWORD;
   		if(isset($data["password"])&&trim($data["password"])!="")
   		{
   			$password = $data["password"];
   		}
   		return new $dbtype($hostname,$dbname,$username,$password);

   }

   protected function openQuery($data)
   {

   		$db = $this->getDbConnection($data);
   		$sql = $data["sql"];
   	    $db->openQuery($sql);
   	    $ret = Array();
   	     if($db->result)
   	     {
              $ret = $db->result;
                      
        }   
           return $ret;
   }
}