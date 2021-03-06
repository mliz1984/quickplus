<?php
    require_once(dirname(__FILE__)."/include.php");
   require_once($_SERVER['DOCUMENT_ROOT']."/vendor/autoload.php");
    use Quickplus\Lib\QuickFormConfig;
    use Quickplus\Lib\quickFormDrawer;
    use Quickplus\Lib\Tools\ArrayTools;
    $db =  new QuickFormConfig::$SqlType();
    $formMark = ArrayTools::getValueFromArray($_REQUEST,'formmark');
    $isreport =  ArrayTools::getValueFromArray($_REQUEST,'isreport');
    $parent = ArrayTools::getValueFromArray($_REQUEST,'parent');
    
    if(intval($isreport)==1)
    {
    	$reportDesigner = new reportDesigner();
        $form = $reportDesigner->getQuickForm($db,intval($formMark));
    }
    else
    {
    	$form = new $formMark();
    }
   // $form->setLoginCheck(false);
    if($form->initDb()!=null)
    {
            $db =$form->initDb();
    }
    $searchPrefix = $form->getAdvanceSearchPrefix().$parent."_";
    $form->setAdvanceSearchParentID($parent);
    $form->setSearchPrefix($searchPrefix);
    $quickFormDrawer = new quickFormDrawer();
    $obj = $quickFormDrawer->setQuickForm($db,$form,$_REQUEST,true);
    $typeSelectId = "searchtype_".$form->getAdvanceSearchPrefix().$parent;
    $typeSelect = new HtmlElement($typeSelectId,$typeSelectId);
    $typeSelect->setParam("class","form-control");
    $typeArray = Array("Include"=>"IN","Not Include"=>"NOT IN");
    $html = "<table width='100%'>";
    $html.="<tr><td>Search Type</td><td><div class='row'><div class='col-md-12'>".$typeSelect->getSelect($typeArray)."</div></div>;</td></tr>";
    $html.= $quickFormDrawer->getSearchBarhtml($src,false,false);
    $html.="</table>";
    echo StringTools::conv($html);
?>