<?php
    require_once(dirname(__FILE__)."/include.php"); 
	require_once(dirname(dirname(__FILE__))."/quickDesigner.php");
		require_once(dirname(dirname(__FILE__))."/commonTools.php");
	$row = $_REQUEST["row"];
    $col = $_REQUEST["col"];
	if($row!=null&&trim($row)!=""&&$col!=null&&trim($col)!="")
	{
	    $db = new QuickFormConfig::$SqlType();
		$value = htmlspecialchars_decode($_REQUEST["value"]);
		$classname = $_REQUEST["classname"];
		$quickForm = new $classname();
		$quickFormDrawer = new quickFormDrawer();
		$obj = $quickFormDrawer->setQuickForm($db, $quickForm, $_REQUEST, true);
		$stringArray = StringTools::escapeJsString($obj->getCellString($col,$row,$value));
		$position = $col.$row;
		$result = Array("position"=>strtolower($position),"value"=>StringTools::escapeJsString($value),"stringArray"=>$stringArray);
		echo ArrayTools::arrayToJson($result);	
	}


?>