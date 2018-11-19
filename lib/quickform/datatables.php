<?php
  require_once(dirname(__FILE__)."/include.php");
      require_once($_SERVER['DOCUMENT_ROOT']."/vendor/autoload.php");
    use Quickplus\Lib\QuickFormConfig;
    use Quickplus\Lib\QuickFormDrawer;
    use Quickplus\Lib\Tools\ArrayTools;
    $db =  new QuickFormConfig::$SqlType();
    $formid = ArrayTools::getValueFromArray($_REQUEST,'formid');
    $formMark = ArrayTools::getValueFromArray($_REQUEST,'formMark');
    $start = ArrayTools::getValueFromArray($_REQUEST,'start');
    $pagerows = ArrayTools::getValueFromArray($_REQUEST,'length');
    $page = $start/$pagerows + 1;
    $isreport =  ArrayTools::getValueFromArray($_REQUEST,'isreport');
    $json = ArrayTools::getValueFromArray($_REQUEST,'json');
    $draw = ArrayTools::getValueFromArray($_REQUEST,'draw');
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