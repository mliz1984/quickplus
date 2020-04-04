<?php
  require_once(dirname(__FILE__)."/include.php");
  require_once($_SERVER['DOCUMENT_ROOT']."/vendor/autoload.php");
    use Quickplus\Lib\QuickFormConfig;
    use Quickplus\Lib\quickFormDrawer;
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
    $dashboardid = ArrayTools::getValueFromArray($_REQUEST,'dashboardid');
    $datatableid = ArrayTools::getValueFromArray($_REQUEST,'datatableid');
    $dataKey = ArrayTools::getValueFromArray($_REQUEST,'dataKey');
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
    $form->setBlank(false);
    $obj = $quickHtmlDrawer->setQuickForm($db,$form);
    

    if($obj->getDb()!=null)
    {
       $db =  $obj->getDb();   
    }
    $obj = $quickHtmlDrawer->getForm($db,$_REQUEST,$page,$pagerows,false,false,"dataTablesOrderMethod","dataTablesSearchMethod");
    $resultParts = Array();
   
    $dataParts = false; 
    $method = $obj->getDashboardDataProcessMethod($dashboardid);
    if(!empty($method)&&!empty($dataKey))
    {
        $result = $obj->getResult($method);
        $resultParts = $obj->$method($dashboardid,$result,$src);
        if(is_array($resultParts[$dataKey]))
        {
            $result = $resultParts[$dataKey];
            $obj->setResult($result);
            $dataParts = true;
        }
    }
    $colArray = $obj->loadDataTableColSetting($dashboardid,$datatableid);
    if(intval(trim($json))==1)
    {
        echo $quickHtmlDrawer->getDataTableJson($draw,$obj,$colArray,$dataParts);
    }
    else
    {
        echo $quickHtmlDrawer->getDataTableHtml($obj,true,false,true);
    }
 ?> 