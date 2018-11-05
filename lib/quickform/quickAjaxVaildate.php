<?php
 require_once(dirname(__FILE__)."/include.php");
    $db =  new QuickFormConfig::$SqlType();
    $formMark = $_REQUEST['formMark'];
    $quickAjaxMethod = $_REQUEST['quickAjaxMethod'];
    $isreport =  $_REQUEST['isreport'];
    $mainid =  $_REQUEST['mainid'];
    $dbname =  $_REQUEST['dbname'];
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