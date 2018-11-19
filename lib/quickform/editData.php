<?php 
   require_once(dirname(__FILE__)."/include.php");
   require_once($_SERVER['DOCUMENT_ROOT']."/vendor/autoload.php");
   use Quickplus\Lib\QuickFormConfig;
   use Quickplus\Lib\Tools\ArrayTools;
   use Quickplus\Lib\quickFormDrawer;
   use Quickplus\Lib\Tools\StringTools;

    //print_r($_REQUEST);
    session_start();
    $db =  new QuickFormConfig::$SqlType();
    $testing = 0;
    $formMark = ArrayTools::getValueFromArray($_REQUEST,'ed_formmark');
    $isreport =  ArrayTools::getValueFromArray($_REQUEST,'ed_isreport');
    $id = ArrayTools::getValueFromArray($_REQUEST,"ed_dataid");
    $processname= ArrayTools::getValueFromArray($_REQUEST,"ed_processname");
    $method = ArrayTools::getValueFromArray($_REQUEST,"method");
    $addMode = true;

    if($id!=null&&trim($id)!="")
    {
          $addMode = false;
    }
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
    
    ?>
    <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
  <html xmlns="http://www.w3.org/1999/xhtml">
  <head>
  <meta http-equiv="Content-Type" content="text/html;charset=<?php echo QuickFormConfig::$encode?>" >
  <link rel="stylesheet" href="/js/quickform/lobibox/css/lobibox.min.css"/>
  <script src="/js/quickform/lobibox/js/lobibox.min.js"></script>

</head>
  <body >

    <?php
$processMethod = null;

    $form->setIsAddMode($addMode);
    $quickFormDrawer = new quickFormDrawer();
    if($processname!=null&&trim($processname)!="")
    {
      $form->initCustomProcessMethod($_REQUEST);
      $processMethod = $form->getCustomProcessMethod($processname);
      $form->setInitEditMethod($processMethod["initeditmethod"]);
    }
    $obj = $quickFormDrawer->setQuickForm($db,$form,$_REQUEST,true);
    
    echo $form->getScriptStr();
    echo $form->loadValidateJs(true);
 

    $warning ='<script language="javascript" >Lobibox.alert("error", {msg:"Operation failed, please check your data."});</script>';
    $js = '<script language="javascript" >window.opener._refreshPage("_anchor_'.$id.'");';
    if(!$form->getDebug())
    {
         $js .= 'window.close();';
    }
    $js .= '</script>';
    if($method!=null&&trim($method)!="")
    {  
      
        $methodResult = $form->execMethod($db,$method,$_REQUEST);

        unset($_SESSION["editdata_session_check"]);
        if($methodResult)
        {
           
            echo $js;
        }
        else
        {
            $tmp = $form->getMethodWarning($method);
            if($tmp!=null&&trim($tmp)!="")
            {
                
                $warning = '<script language="javascript" >Lobibox.alert("error", {msg:"'.$tmp.'"});</script>';

            }
            echo  $warning;
        }
        
    }
   
    $dataArray = array();
    $edit = false;
    if($id!=null&&trim($id)!="")
    {
      $edit = true;
      $dataArray = $form->getFormDataByMainId($db, $id);
     
    }
    $form->initEditCustomField($db,$id,true,$dataArray);

    $temp = $form->processData($db,$_REQUEST,$dataArray,$edit);
    if(is_array($temp))
    {
      $dataArray = $temp;
    }
        ?>
    
  
    <form  class="form-horizontal" name="editData" id="editData" action="<?php echo QuickFormConfig::$quickFormBasePath?>quickform/editData.php" method="post" enctype="multipart/form-data" >
    <div id="master">
     <div id="content"> 
     
    <div id="editDiv" class="table-responsive" style="overflow: visible">
     <table   class="table table-responsive  table-hover table-condensed" width="90%">
      <input type="hidden" id="method" name="method" /> 
      <input type="hidden" id="ed_dataid" name="ed_dataid" value="<?php echo $id; ?>"  /> 
      <input type="hidden" id="deleteid" name="deleteid" value="<?php echo $id; ?>"  /> 
      <input type="hidden" id="ed_isreport" name="ed_isreport" value="<?php echo $isreport; ?>"/> 
      <input type="hidden" id="ed_processname" name="ed_processname" value="<?php echo $processname; ?>"/>
      <?php
            $_SESSION["editdata_session_check"] = StringTools::getRandStr(15);
      ?>
      <input type="hidden" id="editdata_session_check" name="editdata_session_check" value="<?php echo $_SESSION["editdata_session_check"] ?>"/> 
    <?php 
     echo   $form->getEditHiddenStr($dataArray,$_REQUEST);
      
      if($form->isAdd()||$form->isEdit()||$processMethod!=null){ 
        $htmlArray = $form->getEditTopHtml();
        for($i=0;$i<count($htmlArray);$i++)
        {
            $mName = $htmlArray[$i];    
        ?>
            <tr><td colspan='2'  align="right"><?php echo $form->$mName($db);?></td></tr>

        <?php }
    
        $editField = $form->getEditField();
        
        foreach($editField as $dbname => $methodname)
        {
           $displayname = $quickFormDrawer->getDisplayName($dbname,true,true);
             if($quickFormDrawer->getEditMode($dbname,$dataArray)!=null)
             {
        ?>
                 <tr class="form-group" nowrap><td ><b><?php echo  htmlspecialchars_decode($displayname)?>:</b></td>
                 <td id="td_<?php echo $dbname;?>" >  <?php echo $quickFormDrawer->getEditMode($dbname,$dataArray)?></td></tr>
     <?php    }}
        $editCustomField = $form->getEditCustomField();
        foreach($editCustomField as $cid => $customArray)
        {
             $displayname = $customArray["name"];
         ?>
              <tr class="form-group" ><td width="20%"><label><?php echo  htmlspecialchars_decode($displayname)?>:</label></td>
              <td>  <?php echo $form->showEditCustomShowMode($customArray);?></td></tr>
       <?php
        }  
        $extendTable = $form->getExtendTable();
        foreach($extendTable as $extendTableId => $extendTableInfo)
        {
           $html =  $form->getExtendTableHtml($extendTableId,$dataArray);
           $form->addEditCustomHtml($extendTableId,$extendTableInfo["tabletitle"],$html);
        }
        $listTable = $form->getListTable();
        foreach($listTable as $listTableId => $listTableInfo)
        {
           $html =  $form->getListTableHtml($listTableId,$dataArray);
            $form->addEditCustomHtml($listTableId,$listTableInfo["tabletitle"],$html);
        }
        $editCustomHtml = $form->getEditCustomHtmls();
        foreach($editCustomHtml as $chid=>$chArray)
        {
            $chdname = $chArray["name"];
            $chhtml =  $chArray["html"];?>
          <tr class="form-group" ><td width="20%"><label><?php echo  htmlspecialchars_decode($chdname)?>:</label></td>
              <td>  <?php echo htmlspecialchars_decode($chhtml);?></td></tr>
        <?php
        }
        $add = true;

        if($id!=null&&trim($id)!="")
        {
            $add =false;
        }
       if($processMethod!=null&&is_array($processMethod))
       {?>
          <tr><td colspan="3" align="center"> 
            <?php echo $obj->getEditProcessButtonHtml($processname);?>
          </td></tr>
       <?php }
       else
       {?>
           <tr><td colspan='3' align="center">
          <?php if($add)
          {?>
          <input type="submit" Value="Add" onClick="_submit('add')" /> 
         
          <?php } else {?>
          <input type="submit" Value="Update" onClick="_submit('update')" /> 
          
          <?php if($form->isAdd()){ ?>
              <input type="submit" Value="Copy" onClick="_submit('add')" />
          <?php } ?>

          <?php if($form->isDelete()&&$id!=null&&trim($id)!="")
            { ?>
              <input type="button" Value="Delete" onClick="_delete()" />
          <?php } ?>
       
        <?php } 
      }?>
         
        
        <?php $htmlArray = $form->getEditButtonHtml(); 
        for($i=0;$i<count($htmlArray);$i++)
        {
            $mName = $htmlArray[$i];    
        ?>
            <?php echo $obj->$mName($db);?>

        <?php }?>
      </td></tr>
        <?php $htmlArray = $form->getEditButtomHtml(); 

        for($i=0;$i<count($htmlArray);$i++)
        {
            $mName = $htmlArray[$i];    
        ?>
            <tr><td colspan='2'  align="right"><?php echo $form->$mName($db);?></td></tr>

        <?php }?>
      <?php }
        echo $form->getCheckRuleJs("editData");
        echo $form->getValidateScript("editData");
        echo $form->getAttachJs("edit");
        echo $form->getCustomJs(true);
      ?> 
       
      
  </table>
  </div>
    </div>
      </div>
   </form>
   </body>
   <script language="javascript" >
   
    function _submit(method)
    {   
        
        document.getElementById("method").value = method;
         
        var check = true;   
        <?php 
        $rules = $form->getValidateRules();
        if(count($rules)>0)
        {
          foreach($rules["rules"] as $rulesid=>$rules )
          {?>
           if(check)
           {
                check = $("#editData").validate().element($("#<?php echo $rulesid;?>"));  
           }
           else
           {
              return false;
           }
        <?php }
        }?>
        if(check)
        {
          document.editData.submit();
        }
        
    }
    function _delete()
    {   
        document.getElementById("method").value = "delete";
        
         <?php if($form->getDeteleValidateMethod()!=null&&trim($form->getDeteleValidateMethod())!=null)
            {?>

                 $.post("<?php echo QuickFormConfig::$quickFormBasePath?>quickform/quickAjaxDeleteVaildate.php",{ids:'<?php echo $id;?>',isreport:'<?php echo intval($form->isReport());?>',formMark:'<?php echo $form->getFormMark();?>',method:'<?php echo $form->getDeteleValidateMethod();?>'},function(result){
                     result = $.trim(result);
                     if(result=="true")
                     {
                          document.editData.submit();    
                     }
                     else
                     {
                         alert(result);
                         return false;
                     }
                 });
            <?php }else{?>
            document.editData.submit();    
           <?php }?>
        
    
    }
    </script>

<?php echo  $obj->getCustomJs();?></html>
