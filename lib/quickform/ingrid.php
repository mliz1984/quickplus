<?php
    require_once(dirname(__FILE__)."/include.php");
    $db =  new QuickFormConfig::$SqlTyoe();
    $formid = $_REQUEST['formid'];
    $formMark = $_REQUEST['formMark'];
    $page = $_REQUEST['page'];
    $pagerows = $_REQUEST['pagerows'];
    $isreport =  $_REQUEST['isreport'];
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
     // $form->setLoginCheck(false);
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
    $obj = $quickHtmlDrawer->getForm($db,$_REQUEST,$page,$pagerows,false,false,"ingridOrderMethod");
    echo $quickHtmlDrawer->getDataTableHtml($obj,false,true,false);
 ?> 