<?php 
  require_once(dirname(__FILE__)."/include.php");
  $id = $_REQUEST["id"];
  $tableid = $_REQUEST["tableid"];
  $class = $_REQUEST["class"];
  $db =  new QuickFormConfig::$SqlType();
  $obj = new $class();
  $obj->initEdit();
  $result = 0;
  $edi = $obj->getExtendTableDataInfo($tableid);
  if(is_array($edi))
  {
    $tablename = $edi["tablename"];
    $key = $edi["key"];
    $data =new Data($db,$tablename,$key);
    if($id!=null&&trim($id)!="")
    {
        $data->set($key,$id);
        if($data->delete())
        {
          $result = 1;
        }
    }
  }
  echo $result;
?>