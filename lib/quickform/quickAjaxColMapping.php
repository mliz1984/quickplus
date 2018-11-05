<?php

 require_once(dirname(__FILE__)."/include.php");

     $db =  new QuickFormConfig::$SqlType();
    $src = CommonTools::getDataArray($_REQUEST,"ed_");
    $formMark = $src['formmark'];
    $ajaxMethod = $src['ajaxmethod'];
    $isreport =  $src['isreport'];
    $type =  $src['colmappingtype'];
    $keyvalue =  $src['colmappingkey'];
    $mainid =  $src['colmappingmainid'];
    $choosedvalue =  $src['colmappingchoosedvalue'];
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