    <?php
         require_once(dirname(__FILE__)."/include.php");
   require_once($_SERVER['DOCUMENT_ROOT']."/vendor/autoload.php");
    use Quickplus\Lib\QuickFormConfig;
    use Quickplus\Lib\quickFormDrawer;
    use Quickplus\Lib\Tools\ArrayTools;
        $db =  new QuickFormConfig::$SqlType();
        $formMark = ArrayTools::getValueFromArray($_REQUEST,'formmark');
        $isreport =  ArrayTools::getValueFromArray($_REQUEST,'isreport');
        $dbname =  ArrayTools::getValueFromArray($_REQUEST,'dbname');
        $method = ArrayTools::getValueFromArray($_REQUEST,"method");
        $keyword = ArrayTools::getValueFromArray($_REQUEST,"keyword");
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
        $form = $quickFormDrawer->setQuickForm($db,$form);
        $array =  $form->$method($db,$dbname,$keyword,$_REQUEST);
         echo trim(json_encode($array));


    ?>