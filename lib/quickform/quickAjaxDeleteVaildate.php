<?php

 require_once(dirname(__FILE__)."/include.php");
    $db =  new QuickFormConfig::$SqlType();
    $formMark = $_REQUEST['formMark'];
    $method = $_REQUEST['method'];
    $isreport =  $_REQUEST['isreport'];
    $ids =  $_REQUEST['ids'];
    if(intval($isreport)==1)
    {
    	$reportDesigner = new reportDesigner();
        $form = $reportDesigner->getQuickForm($db,intval($formMark));
    }
    else
    {
    	$form = new $formMark();
    }
  //    $form->setLoginCheck(false);
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
    $result ="You should implement method '".$method. "' in your quickform class.";
    if(method_exists($obj,$method))
    {
        $result = $obj->$method($db,$ids);
    }
    if(is_bool($result))
    {
        if($result)
        {
            $result ="true";
        }
        else
        {
              $result ="false";
        }
    }
    echo $result;
?>