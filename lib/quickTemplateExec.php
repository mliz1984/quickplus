<?php
	require_once(dirname(__FILE__)."/parameters.php");
    require_once(dirname(__FILE__)."/dbmodule.php");
    require_once(dirname(__FILE__) . "/quickFormConfig.php");
	require_once(dirname(__FILE__) . "/DataMsg.php");
	require_once(dirname(__FILE__)."/quickTemplateImpl.php");
    $db = new QuickFormConfig::$SqlType();
	$id = $_REQUEST["id"];
	$for = $_REQUEST["for"];
	$result = false;
	if($id!=null&&trim($id)!=null)
	{
		$quickTemplate = new QuickTemplate();
 		$data = new Data($db,$quickTemplate->getDbTableName(),"id");
 		$data->set("id",$id);
 		$data->findByPrimaryKey();
 		if($data!=null)
 		{
 			$classname = $data->getString("classname");
 			$obj = new $classname();
 			$editPrefix = $obj->getEditPrefix();
 			$searchPrefix = $obj->getSearchPrefix();
 			$src["classname"] = $classname;
 			$src["classsrc"] = $data->getString("classsrc");
 			$dbname =  $data->getString("col");
 			$src["qt_col"] = $dbname;
 	 		$src["qt_fixvalue"] = $data->getString("fixvalue");
 	 		$src["qt_qtmethod"]= $data->getString("qtmethod");
 	 		$src[$editPrefix."content"] = $data->getString("content");
 	 		$src[$editPrefix."title"] = $data->getString("title");
 	 		$src["qt_templateid"] = $id;
 			$rangeStr = $data->getString("range");
 			$range = json_decode($rangeStr);
 			foreach($range as $key => $value)
 			{
 				$src[$searchPrefix.$key] = $value;
 			}
 			$colInfo = $obj->getColInfo();
 			$dbcol = $colInfo[$dbname];
 			$where = null;
 			if($for!=null&&trim($for)!="")
 			{
 				$where = $dbcol." = '".$for."'";
 			}
 			$result =  $quickTemplate->execTemplateMethod($db,$src,$editPrefix,$where);
 		}
	}
	echo strval($result);

     
?>