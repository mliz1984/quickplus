<?php
    require_once(dirname(__FILE__)."/include.php");
    $db =  new QuickFormConfig::$SqlType();
    $formMark = $_REQUEST['formmark'];
    $isreport =  $_REQUEST['isreport'];
    $dbname =  $_REQUEST['dbname'];
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
    $quickFormDrawer = new quickFormDrawer();
    $obj = $quickFormDrawer->setQuickForm($db,$form,$_REQUEST,true);
    if($obj->getDb()!=null)
    {
       $db =  $obj->getDb();   
    }
    $src = CommonTools::getDataArray($_REQUEST,"vajax_");
    echo $quickFormDrawer->getEditMode($dbname,$src);

?>