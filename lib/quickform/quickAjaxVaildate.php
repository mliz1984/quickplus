<?php
 require_once(dirname(__FILE__)."/include.php");
   require_once($_SERVER['DOCUMENT_ROOT']."/vendor/autoload.php");
    use Quickplus\Lib\QuickFormConfig;
    use Quickplus\Lib\quickFormDrawer;
    use Quickplus\Lib\Tools\ArrayTools;
    use Quickplus\Lib\Tools\CommonTools;
    $db =  new QuickFormConfig::$SqlType();
    $formMark = ArrayTools::getValueFromArray($_REQUEST,'formMark');
    $quickAjaxMethod = ArrayTools::getValueFromArray($_REQUEST,'quickAjaxMethod');
    $isreport =  ArrayTools::getValueFromArray($_REQUEST,'isreport');
    $mainid =  ArrayTools::getValueFromArray($_REQUEST,'mainid');
    $dbname =  ArrayTools::getValueFromArray($_REQUEST,'dbname');
    if(intval($isreport)==1)
    {
    	$reportDesigner = new reportDesigner();
        $form = $reportDesigner->getQuickForm($db,intval($formMark));
    }
    else
    {
    	$form = new $formMark();
    }
    //  $form->setLoginCheck(false);
     if($form->initDb()!=null)
    {
            $db =$form->initDb();
    }
    $quickFormDrawer = new quickFormDrawer();
    $obj = $quickFormDrawer->setQuickForm($db,$form,$_REQUEST,true);
    if($obj->getDb()!=null)
    {
       $db =  $obj->getDb();   
    }
    $src = CommonTools::getDataArray($_REQUEST,"quickAjax_");
    $result = $obj->$quickAjaxMethod($db,$dbname,$mainid,$src);
    if(!is_array($result))
    {
        if($result)
        {
            $result = "true";
        }
        else
        {
            $result = "fasle";
        }
        $message = $obj->getVaildateRulesMessage($dbname,"quickAjax");   
        $result = Array("success"=>$result,"message"=>$message);
    }
    else if(is_bool($result["success"]))
    {
        if($result["success"])
        {
            $result["success"] = "true";
        }
        else
        {
            $result["success"] = "false";
        }
    }
    $result["message"] = iconv(QuickFormConfig::$encode, "UTF-8", $result["message"]);
    echo ArrayTools::arrayToJson($result);
?>