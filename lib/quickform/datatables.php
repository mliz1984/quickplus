<?php
  require_once(dirname(__FILE__)."/include.php");
   
    $db =  new QuickFormConfig::$SqlType();
    $formid = $_REQUEST['formid'];
    $formMark = $_REQUEST['formMark'];
    $start = $_REQUEST['start'];
    $pagerows = $_REQUEST['length'];
    $page = $start/$pagerows + 1;
    $isreport =  $_REQUEST['isreport'];
    $json = $_REQUEST['json'];
    $draw = $_REQUEST['draw'];
    $form = null;

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
    $quickHtmlDrawer = new quickHtmlDrawer($formid);
    $obj = $quickHtmlDrawer->setQuickForm($db,$form);


    if($obj->getDb()!=null)
    {
       $db =  $obj->getDb();   
    }
    $obj = $quickHtmlDrawer->getForm($db,$_REQUEST,$page,$pagerows,false,false,"dataTablesOrderMethod","dataTablesSearchMethod");
 
    if(intval(trim($json))==1)
    {

        echo $quickHtmlDrawer->getDataTablesJson($draw,$obj);
    }
    else
    {
            echo $quickHtmlDrawer->getDataTableHtml($obj,true,false,true);
    }
 ?> 