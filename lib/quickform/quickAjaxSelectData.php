    <?php
         require_once(dirname(__FILE__)."/include.php");
        $db =  new QuickFormConfig::$SqlType();
        $formMark = $_REQUEST['formmark'];
        $isreport =  $_REQUEST['isreport'];
        $dbname =  $_REQUEST['dbname'];
        $method = $_REQUEST["method"];
        $keyword = $_REQUEST["keyword"];
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