<?php
 require_once(dirname(__FILE__)."/include.php");

    /*$colSign = ArrayTools::getValueFromArray($_REQUEST,"colsign"];
    $src = CommonTools::getDataArray($_REQUEST,$colSign);
    print_r($src);*/
    $db =  new QuickFormConfig::$SqlType();
    $formMark = ArrayTools::getValueFromArray($_REQUEST,'formmark');
    $isreport =  ArrayTools::getValueFromArray($_REQUEST,'isreport');
    $method= ArrayTools::getValueFromArray($_REQUEST,"method");
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
   //   $form->setLoginCheck(false);
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
    $form->$method($_REQUEST);
?>