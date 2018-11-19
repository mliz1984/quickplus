    <?php
         require_once(dirname(__FILE__)."/include.php");
    require_once($_SERVER['DOCUMENT_ROOT']."/vendor/autoload.php");
    use Quickplus\Lib\QuickFormConfig;
    use Quickplus\Lib\QuickFormDrawer;
    use Quickplus\Lib\Tools\ArrayTools;
        $db =  new QuickFormConfig::$SqlType();
        $formMark = ArrayTools::getValueFromArray($_REQUEST,'formmark');
        $isreport =  ArrayTools::getValueFromArray($_REQUEST,'isreport');
        $dbname =  ArrayTools::getValueFromArray($_REQUEST,'dbname');
        $method =  ArrayTools::getValueFromArray($_REQUEST,'method');
        $id = ArrayTools::getValueFromArray($_REQUEST,"id");
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
        $whereClause = $form->getMainIdOriDbName()." = '".$id."'";
        $form->setFinalWhereClause($whereClause);
        $quickFormDrawer = new quickFormDrawer();
        $quickFormDrawer->setQuickForm($db,$form);
        $quickFormDrawer->setBlank(false);
        $obj = $quickFormDrawer->getForm($db,$_REQUEST,1,0,$isExport,false);
        echo $obj->$method(0,$dbname,false);



    ?>