<?php
require_once($_SERVER['DOCUMENT_ROOT']."/vendor/autoload.php");
    use Quickplus\Lib\Tools\ArrayTools;
    use Quickplus\Lib\QuickFormConfig;
    use Quickplus\Lib\quickFormDrawer;
header('Content-Type: text/html; charset=utf-8');
        //echo '[{"title":"PrimeCables","key":"1000","expanded":false},{"title":"(*) FactoryGlobal","key":"3000","expanded":false}]';
        
	    require_once(dirname(__FILE__)."/include.php");
	    $db =  new QuickFormConfig::$SqlType();
        $formMark = ArrayTools::getValueFromArray($_REQUEST,'formmark');
        $isreport =  ArrayTools::getValueFromArray($_REQUEST,'isreport');
        $method =  ArrayTools::getValueFromArray($_REQUEST,'method');
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
         if($form->initDb()!=null)
        {
                $db =$form->initDb();
        }
        $quickFormDrawer = new quickFormDrawer();
      //  $form->setLoginCheck(false);
        $form = $quickFormDrawer->setQuickForm($db,$form);
        echo $form->$method($_REQUEST);
?>