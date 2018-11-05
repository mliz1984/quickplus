    <?php
         require_once(dirname(__FILE__)."/include.php");
        $db =  new QuickFormConfig::$SqlType();
        $formMark = $_REQUEST['formmark'];
        $isreport =  $_REQUEST['isreport'];
        $dbname =  $_REQUEST['dbname'];
        $method =  $_REQUEST['method'];
        $id = $_REQUEST["id"];
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