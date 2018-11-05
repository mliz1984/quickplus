<?php
 require_once(dirname(__FILE__)."/include.php");

    $db =  new QuickFormConfig::$SqlType();
    $tablename = $_REQUEST["tablename"];
    $findkey = $_REQUEST["findkey"];
    $value = trim($_REQUEST["value"]);
    $whereClause =  $_REQUEST["whereClause"];
    $sql = "SELECT ".$findkey." FROM ".$tablename." WHERE ".$findkey." LIKE '%".$value."%'";
    if($whereClause!=null&&trim($whereClause)!="")
    {
        $sql.=" AND ".$whereClause;
    }
    $dataMsg = new DataMsg();
    $dataMsg->findBySql($db,$sql);
      $result = "clear";
    if($dataMsg->getSize()>0)
    {
        $result = $dataMsg->getSize();

        if($result==1)
        {
            $data = $dataMsg->getData(0,true);
            if(strtolower($value)==StringTools::conv(strtolower($data[$findkey])))
            {
                $result = "submit";
            }
        }
    }
    echo $result;
?>