<?php

 require_once(dirname(__FILE__)."/include.php");
   require_once($_SERVER['DOCUMENT_ROOT']."/vendor/autoload.php");
    use Quickplus\Lib\QuickFormConfig;
    use Quickplus\Lib\QuickFormDrawer;
    use Quickplus\Lib\Tools\ArrayTools;
     $db =  new QuickFormConfig::$SqlType();
    $src = CommonTools::getDataArray($_REQUEST,"ed_");
    $formMark = ArrayTools::getValueFromArray($src,'formmark');
    $ajaxMethod = ArrayTools::getValueFromArray($src,'ajaxmethod');
    $isreport =  ArrayTools::getValueFromArray($src,'isreport');
    $type =  ArrayTools::getValueFromArray($src,'colmappingtype');
    $keyvalue =  ArrayTools::getValueFromArray($src,'colmappingkey');
    $mainid =  ArrayTools::getValueFromArray($src,'colmappingmainid');
    $choosedvalue =  ArrayTools::getValueFromArray($src,'colmappingchoosedvalue');
    if(intval($isreport)==1)
    {
        $reportDesigner = new reportDesigner();
        $form = $reportDesigner->getQuickForm($db,intval($formMark));
    }
    else
    {
        $form = new $formMark();
    }
     // $form->setLoginCheck(false);
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
    $result = json_encode($obj->getHtmlArrayForColMapping($type,$obj->$ajaxMethod($choosedvalue,$_REQUEST),$keyvalue,$mainid));
    echo $result;
   
?>  