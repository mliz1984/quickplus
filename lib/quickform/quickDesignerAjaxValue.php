<?php
    require_once(dirname(__FILE__)."/include.php"); 
   require_once($_SERVER['DOCUMENT_ROOT']."/vendor/autoload.php");
    use Quickplus\Lib\QuickFormConfig;
    use Quickplus\Lib\quickFormDrawer;
    use Quickplus\Lib\Tools\ArrayTools;

	$row = ArrayTools::getValueFromArray($_REQUEST,"row");
    $col = ArrayTools::getValueFromArray($_REQUEST,"col");

	if($row!=null&&trim($row)!=""&&$col!=null&&trim($col)!="")
	{
	    $db = new QuickFormConfig::$SqlType();
		$value = htmlspecialchars_decode(ArrayTools::getValueFromArray($_REQUEST,"value"));
		$classname = ArrayTools::getValueFromArray($_REQUEST,"classname");
		$quickForm = new $classname();
		$quickFormDrawer = new quickFormDrawer();
		$obj = $quickFormDrawer->setQuickForm($db, $quickForm, $_REQUEST, true);
		$stringArray = StringTools::escapeJsString($obj->getCellString($col,$row,$value));
		$position = $col.$row;
		$result = Array("position"=>strtolower($position),"value"=>StringTools::escapeJsString($value),"stringArray"=>$stringArray);
		echo ArrayTools::arrayToJson($result);	
	}


?>