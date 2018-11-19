<?php 
    require_once(dirname(__FILE__)."/include.php");
       require_once($_SERVER['DOCUMENT_ROOT']."/vendor/autoload.php");
    use Quickplus\Lib\QuickFormConfig;
    use Quickplus\Lib\QuickFormDrawer;
    use Quickplus\Lib\Tools\ArrayTools;
    $db =  new QuickFormConfig::$SqlType();
  
    $formMark = ArrayTools::getValueFromArray($_REQUEST,'ed_formmark');
    $isreport =  ArrayTools::getValueFromArray($_REQUEST,'ed_isreport');
    $id = ArrayTools::getValueFromArray($_REQUEST,"ed_dataid");
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
    $warning ='<script language="javascript" >alert("Operation failed, please check your data.");</script>';
    $js = '<script language="javascript" >window.opener._search();window.close();</script>';  
    if($method!=null&&trim($method)!="")
    {
        if($form->execMethod($db,$method,$_REQUEST))
        {
            echo $js;
        }
        else
        {
            echo  $warning;
        }
    }
   
    $dataArray = array();
    $edit = false;
    if($id!=null&&trim($id)!="")
    {
      $dataArray = $form->getFormDataByMainId($db, $id);
      $edit = true;
      $result = array();
      $result[] = $dataArray;
      $form->setResult($result);

    }
    $temp = $form->processData($db,$_REQUEST,$dataArray,$edit);
    if(is_array($temp))
    {
    	$dataArray = $temp;
    }
    ?>
    <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
	<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
	<meta http-equiv="Content-Type" content="text/html;charset=<?php echo QuickFormConfig::$encode?>" >


    <?php echo $form->getScriptStr()?>
    <script language="javascript" >
    function _close()
    {
        window.close();
    }
    function _submit(method)
    {
        document.getElementById("method").value = method;
        document.editData.submit();
    }
    </script>
    <br><br><br>
    <div id="master">
     <div id="content"> 
     <div class="container">
     <div class="row">
     <div class="col-md-2"></div>
      <div class="col-md-8">
    <div id="editDiv" class="table-responsive" >

    <form   name="editData" id="editData" action="<?php echo QuickFormConfig::$quickFormBasePath?>quickform/viewData.php" method="post" >
     <table   class="table table-responsive  table-hover table-condensed">
      <input type="hidden" id="method" name="method" /> 
      <input type="hidden" id="ed_formmark" name="ed_formmark" value="<?php echo $formMark; ?>" /> 
      <input type="hidden" id="ed_dataid" name="ed_dataid" value="<?php echo $id; ?>"  /> 
      <input type="hidden" id="deleteid" name="deleteid" value="<?php echo $id; ?>"  /> 
      <input type="hidden" id="ed_isreport" name="ed_isreport" value="<?php echo $isreport; ?>"/> 
    <?php 
        $htmlArray = $form->getViewTopHtml();
        for($i=0;$i<count($htmlArray);$i++)
        {
            $mName = $htmlArray[$i];    
        ?>
            <tr class="form-group"><td colspan='2'  align="right"><?php echo $form->$mName($db);?></td></tr>

        <?php }
    
         $detailField = $form->getDetailField();

        foreach($detailField as $dbname => $methodname)
        { 
             $displayname = $quickFormDrawer->getDisplayName($dbname,true);
        ?>
                 <tr class="form-group"><td width="30%">  <?php echo  htmlspecialchars_decode($displayname)?>:</td>
                 <td>  <?php echo htmlspecialchars_decode($form->$methodname(0,$dbname))?></td></tr>

     <?php }?>
 <tr class="form-group"><td colspan='2' align="center">
      
            <input type="button" Value="Close" onClick="_close()" />
       
        <?php $htmlArray = $form->getViewButtonHtml();   
        for($i=0;$i<count($htmlArray);$i++)
        {
            $mName = $htmlArray[$i];    
        ?>
            <?php echo $obj->$mName($db);?>

        <?php }?>
        </td></tr>
        <?php $htmlArray = $form->getViewButtomHtml();   
        for($i=0;$i<count($htmlArray);$i++)
        {
            $mName = $htmlArray[$i];    
        ?>
            <tr class="form-group"><td colspan='2'  align="right"><?php echo $form->$mName($db);?></td></tr>

        <?php }?>

      

	</table>
   </form>

   </div>
    </div>
    <div class="col-md-2"></div>
      </div>
      </div>
    </div>
      </div>