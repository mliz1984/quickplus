<?php

    require_once($_SERVER['DOCUMENT_ROOT']."/lib/parameters.php");
    require_once($_SERVER['DOCUMENT_ROOT']."/lib/dbmodule.php");
    require_once($_SERVER['DOCUMENT_ROOT']."/lib/commonTools.php");  
    require_once($_SERVER['DOCUMENT_ROOT']."/lib/quickFormDrawer.php"); 
    require_once($_SERVER['DOCUMENT_ROOT']."/lib/quickPage.php");   
    require_once($_SERVER['DOCUMENT_ROOT']."/lib/quickLoginManager.php");  
    require_once($_SERVER['DOCUMENT_ROOT']."/lib/quickFormConfig.php");
?>
<?php
    $menuid = intval(ArrayTools::getValueFromArray($_REQUEST,QuickFormConfig::$menuIdMark));
    $subForm = ArrayTools::getValueFromArray($_REQUEST,QuickFormConfig::$subFormMark);
    $loginmanager = QuickLoginManager::getQuickLoginManager();
    $mappingArray = $loginmanager->getMenuClassMapping($menuid);
    $classsrc =ArrayTools::getValueFromArray($mappingArray,"classsrc"); 
    $classname =ArrayTools::getValueFromArray($mappingArray,"classname");  
    require_once($_SERVER['DOCUMENT_ROOT'].$classsrc);
    $url = UrlTools::getFullUrl();
    $blank = ArrayTools::getValueFromArray($_REQUEST,"blank");
    $id = ArrayTools::getValueFromArray($_REQUEST,"id");
    $pageRows= ArrayTools::getValueFromArray($_REQUEST,"pageRows");
    $page =  ArrayTools::getValueFromArray($_REQUEST,"page");
    $method =  ArrayTools::getValueFromArray($_REQUEST,"method");
    $searchSign =   ArrayTools::getValueFromArray($_REQUEST,"searchSign");
    $exportmode =  ArrayTools::getValueFromArray($_REQUEST,"exportmode");
    $qp_keeprowsids = ArrayTools::getValueFromArray($_REQUEST,"qp_keeprowsids");
    $qp_excluderowsids = ArrayTools::getValueFromArray($_REQUEST,"qp_excluderowsids");
    $qp_anchor =ArrayTools::getValueFromArray($_REQUEST,"qp_anchor");
    $isExport = false;
    if($blank=="1")
    {
        $blank = true;
    }
    else {
        $blank = false;
    }
    if($exportmode != null && trim($exportmode)!="")
    {
        $isExport = true;
    }
    $quickFormDrawer = new quickFormDrawer();
    if($page==null||$page=="")
    {
        $page = 1;
    }
    $quickForm = new $classname();
    if($subForm!=null&&trim($subForm)!=null)
    {
       $quickForm->initSubForm();
        $subForm = $quickForm->getSubForm($subForm);
       if(is_array($subForm))
       {
         $classsrc = $subForm["classsrc"];
         $classname = $subForm["classname"];
         require_once($_SERVER['DOCUMENT_ROOT'].$classsrc);
         $quickForm = new $classname();
       }
    }
    $db = $quickForm->initDb();
    if($db==null)
    {
         $db = new QuickFormConfig::$SqlType();
    } 
    $quickForm = $quickFormDrawer->setQuickForm($db,$quickForm);
    if($pageRows==null||trim($pageRows)=="")
    {
        $pageRows = $quickForm->getPageRows();

    }
   

  
    $obj = $quickFormDrawer->getForm($db,$_REQUEST,$page,intval($pageRows),$isExport,$blank);
    if($method!=null&&trim($method)!="")
    {
        $methodResult = $obj->getMethodResult();
        $echo = null;
        $url = null;
        $array = $obj->getMethodSuccess($method);    
        if(is_array($array))
        {
            $echo = $array["msg"];
            $url = $array["url"];
        }
     
        if($echo==null||trim($echo)=="")
        {
            $echo = $obj->getMethodSuccessString();
        }
        $echotype = "success";
        if(!$methodResult)
        {
            $echo = $obj->getMethodWarning($method);
            if($echo==null||trim($echo)=="")
            {
                $echotype = "error";
                $echo = $obj->getMethodFailString();
            }
        }
        if($echo!=null&&trim($echo)!="")
        {
            echo '<script language="javascript" >Lobibox.alert("'.$echotype.'", {msg:"'.$echo.'"});</script>';
        }
        if($methodResult&&$url!=null&&trim($url)!="")
        {
            echo '<script language="javascript" >window.location.href="'.$url.'";</script>';
        }
    }
    $searchField = $obj->getSearchField();
    $totalCount = $obj->getTotalCount();
     $resultSize = $obj->getResultSize();
     
          $totalPages = $obj->getTotalPages();
          $curPage = $obj->getCurPage();
          $startRecord = ($curPage-1)*$pageRows+1;
          $endRecord = $startRecord + $resultSize-1;
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html;charset=<?php echo QuickFormConfig::$encode?>" >

<?php echo $obj->getScriptStr();?>
<?php echo QuickPage::getPageJs($_REQUEST,$obj,$searchSign,"quickTable");?>
</head>
<body >
<div >
     
                       
                    <IFrame id="" name="export_frame" id="export_frame" width="1px" height="1px" style="display:none"></IFrame>
                    <form name="editData" id="editData"  action="<?php echo QuickFormConfig::$quickFormMethodPath?>editData.php" target="_editData" method="post">            
                         <input type="hidden" id="ed_isreport" name="ed_isreport" value="<?php if($obj->isReport()){echo 1;}else{echo 0;}?>"/>     
                         <input type="hidden" id="ed_formmark" name="ed_formmark" value="<?php echo $obj->getFormMark(); ?>"/>
                        <input type="hidden" id="ed_dataid" name="ed_dataid" />
                        <input type="hidden" id="ed_processname" name="ed_processname" value="" />
                         <?php
                            foreach($obj->getTransfer() as $k=>$v)
                            { ?>
                                 <input type="hidden" id="<?php echo $k; ?>" name="<?php echo $k; ?>" value="<?php echo $v; ?>"/> 
                       <?php }?>               
                    </form>
                    <form name="quickForm" id="quickForm" action = "<?php echo $url;?>" method="post" enctype="multipart/form-data">
                                      <input type="hidden" id="curPage" name="curPage" />  
                                      <input type="hidden" id="pageRows" name="pageRows"  value="<?PHP echo $pageRows; ?>" />    
                                     <input type="hidden" id="searchSign" name="searchSign" value="" />     
                                    <input type="hidden" id="method" name="method" value=""/>  
                                         
                                    <input type="hidden" id="deleteid" name="deleteid" />
                                    <input type="hidden" id="exportmode" name="exportmode" value="" />   
                                    <input type="hidden" id="qp_keeprowsids" name="qp_keeprowsids" value="<?PHP echo $qp_keeprowsids; ?>"/>
                                   <input type="hidden" id="qp_excluderowsids" name="qp_excluderowsids" value="<?PHP echo $qp_excluderowsids; ?>"/>
                                   <input type="hidden" id="id" name="id" value="<?PHP echo $id; ?>"/>
                                   <input type="hidden" id="_statistics_isreport" name="_statistics_isreport" value="<?php if($obj->isReport()){echo 1;}else{echo 0;}?>"/>     
                                   <input type="hidden" id="_statistics_formmark" name="_statistics_formmark" value="<?php echo $obj->getFormMark(); ?>"/>
                                   <input type="hidden" id="_statistics_setname" name="_statistics_setname" value=""/>
                                    <input type="hidden" id="qp_anchor" name="qp_anchor" value=""/>

<?php echo $obj->getHiddenStr()?> 
<table width="100%" >
<?php if(($obj->getPageExport()&&$pageRows!=0&&$endRecord>0)||($obj->getExport()&&$totalCount!=0)||($obj->getClear()&&$obj->getSearchBar())||($obj->getSearchBar())){ ?>
 <tr><td>
 <?php 
    $isCollapse = null;
    if(isset($_REQUEST["searchBarCollapseStatus"])&&$_REQUEST["searchBarCollapseStatus"]!=null&&trim($_REQUEST["searchBarCollapseStatus "])!="")
    {
        $tmp = intval(trim($_REQUEST["searchBarCollapseStatus"]));
        if($tmp==1)
        {
            $isCollapse = true;
        }
        else
        {
            $isCollapse = false;
        }
    }
    if($isCollapse == null)
    {
        $isCollapse = $obj->getSearchBarCollapse();
    }
    $collapse = "";
    if(!$isCollapse)
    {
         $collapse = "in";
    }
    if($obj->getSearchBar())
    {
 ?>
 <?php if($obj->getSearchDivMode()){ ?> 
 <button class="btn btn-info " type="button"  onclick="$('#qp_searchBar').css('display','');"><span class="glyphicon glyphicon-search" aria-hidden="true"></span>Search</button>
 <?php }else{?>
 <button class="btn btn-info " type="button" data-toggle="collapse" data-target="#searchBarCollapse" aria-expanded="<?php echo strval($isCollapse);?>" aria-controls="searchBarCollapse">
<span class="glyphicon glyphicon-search" aria-hidden="true"></span>Search</button>
<?php } ?><br>
<div class="collapse <?php echo $collapse;?>" id="searchBarCollapse">
<input type="hidden" id="searchBarCollapseStatus" name="searchBarCollapseStatus" value="<?PHP echo strval($isCollapse); ?>"/>
<?php if($obj->getSearchDivMode()){ ?>
      <div id="qp_searchBar" style="display:none;position:absolute;left:0%;top:0%;z-index:999" class="panel panel-info"><div class="panel-heading"><h4>Search</h4><div class="panel-body"><?php } ?><table width="100%" >
         <?php echo $quickFormDrawer->getSearchBarHtml($_REQUEST);?>
         <tr><td colspan="2" align="right">
          <?php   if($obj->getSearchBar()){?>
            <input type="button" onclick="_newSearch()" value="Search"/>
            <?php }?>
           
            <?php 
                 $exportFormat = $obj->getExportFormat(); 
                 if($obj->getPageExport()&&$pageRows!=0&&$endRecord>0) { 
                   
                    if($obj->haveOtherExportFormat()){
                    ?>
                    <div class="btn-group">
                      <button type="button" class="btn btn-primary" onclick="_pageExport()">Export(Page)</button>
                      <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <span class="caret"></span>
                        <span class="sr-only">Toggle Dropdown</span>
                      </button>
                      <ul class="dropdown-menu">
                      <?php 
                        foreach($exportFormat as $format => $bool)
                        {
                            if($bool)
                            {
                      ?>    
                        <li><a href="javascript:_pageExportWithFormat('<?php echo $format;?>');"><?php echo $format;?></a></li>
                      <?php }}?>
                      </ul>
                  <?php if($obj->getSearchDivMode()){ ?>
                    </div></div>

            <?php }}else{?>
                     <input type="button" onclick="_pageExport()" value="Export(Page)"/>
            <?php }}?>

             <?php  if($obj->getExport()&&$obj->getResultSize()!=0) { 
                      if($obj->haveOtherExportFormat()){
                    ?>
                    <div class="btn-group">
                      <button type="button" class="btn btn-primary" onclick="_export()">Export(All)</button>
                      <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <span class="caret"></span>
                        <span class="sr-only">Toggle Dropdown</span>
                      </button>
                      <ul class="dropdown-menu">
                      <?php 
                        foreach($exportFormat as $format => $bool)
                        {
                            if($bool)
                            {
                      ?>    
                        <li><a href="javascript:_exportWithFormat('<?php echo $format;?>');"><?php echo $format;?></a></li>
                      <?php }}?>
                      </ul>
                    </div>

            <?php }else{?>
                     <input type="button" onclick="_export()" value="Export(All)"/>
            <?php }}?>
             <?php $statisticsGroup = $obj->getStatisticsGroup();
                  if(is_array($statisticsGroup)&&count($statisticsGroup)>0)
                  {?>
                    <div class="btn-group">
                     <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Statistics</button>
                         <ul class="dropdown-menu">
                          <?php foreach($statisticsGroup as $setname=>$array){?>
                            <li><a href="javascript:_showStat('<?php echo $setname;?>')"><?php echo $array["name"]?></a></li>
                            <?php }?>
                          </ul>
                    </div>
                  <?}?> 
                  <?php $chartGroup = $obj->getChartGroup();
                  if(is_array($chartGroup)&&count($chartGroup)>0)
                  {?>
                    <div class="btn-group">
                     <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Charts</button>
                         <ul class="dropdown-menu">
                          <?php foreach($chartGroup as $chartid=>$array){?>
                            <li><a href="javascript:_showChart('<?php echo $chartid;?>')"><?php echo $array["name"]?></a></li>
                            <?php }?>
                          </ul>
                    </div>
                  <?}?> 
                    
             <?php   if($obj->getClear()&&$obj->getSearchBar()) { ?>
                     <input type="button" onclick="_clear()" value="Clear"/>
            <?php }?>
             <?php if($obj->getSearchDivMode()){ ?>
              <button class="btn btn-danger " type="button"  onclick="$('#qp_searchBar').css('display','none');">Close</button>
               <?php }?>
            
             </td>
         </tr>     
     </table></div>
     </div>
 </td>
 </tr>     
 <?php }} 
  $reportName = $quickFormDrawer->getReportName();  
   if($reportName!=null&&trim($reportName)!="")
   { ?>
     <tr><td align="center"><font size="4"><?php echo $reportName;?></font></td></tr>
     <?php
   }        
     if($pageRows!=0&&$totalCount!=0)
     {    
     ?>
     
<tr>
     <td align="right"><font size="3"><?php if($curPage==1) {?>First|Previous<?php }else {?><a href="javascript:_jumpPage(1);"><font color="blue">First</font></a>|<a href="javascript:_jumpPage(<?php echo $curPage-1 ?>);"><font color="blue">Previous</font></a><?php }?>|<?php if($curPage==$totalPages) 
     {?>Next|Last<?php }else{?><a href="javascript:_jumpPage(<?php echo $curPage+1 ?>);"><font color="blue">Next</font></a>|<a href="javascript:_jumpPage(<?php echo $totalPages ?>);"><font color="blue">Last</font></a><?php }?>
         <input type="input" size="<?php echo strlen(strval($totalPages)) ?>" id="pagenum" name="pagenum" maxlength="<?php echo strlen(strval($totalPages)) ?>" value="" />  <input type="button" onclick="_changePage('pagenum',<?php echo $totalPages?>)" value="Go"/></font>
     </td>
    </tr>

 <tr>
     <td align="right"><font size="3"> <?php echo $startRecord?>-<?php echo $endRecord?>/<?php echo $totalCount?> Records  <?php echo $curPage?>/<?php echo $totalPages?> Pages <input type="input" size="<?php echo strlen(strval($totalCount))+1 ?>" id="prows" name="prows" maxlength="<?php echo strlen(strval($totalCount))+1?>" onchange="_changePagerows('prows')" value="<?php echo $pageRows?>"  /> Records/Page Page <?php echo $curPage ?></font></td>
 </tr>
<?php }
     else
     {
?>
 <tr> <td align="right"> <font size="3"> <?php echo $resultSize?> Records</font></td></tr>
<?php }?>
 <?php $htmlArray = $obj->getTopHtml();  
for($i=0;$i<count($htmlArray);$i++)
{
    $mName = $htmlArray[$i];    
?>
<tr><td align="right"><?php echo $obj->$mName($db,$_REQUEST);?></td></tr>

<?php }
$toolbar = $obj->getToolBar();
if($toolbar!=null&&!$blank){
    $l = $toolbar["l"];
    $r = $toolbar["r"]
?>
<tr>
    <td><table width="100%" >
        <tr>
            <td align="left"><?php if($l!=null&&trim($l)!=""){echo $obj->$l($db);}?></td>
               <td align="right"> <?php if($r!=null&&trim($r)!=""){echo $obj->$r($db);}?></td>
        </tr>
    </table>
    </td>
<?php }?>
    
</tr>

<tr>
    <td>
    <table>
    <tr><td>
    <?php if($obj->isAdd()){?>
            <input type="button" Value="Add" onClick="_add()" />
    <?php }?>
     <?php if($obj->isEdit()){?>
            <input type="button" Value="Edit" onClick="_edit()" /> 
     <?php }?>        
    
     <?php 
        $customProcessMethod = $obj->getCustomProcessMethod();
        foreach($customProcessMethod as $processName =>$methodInfo)
        {   
            echo $obj->getProcessButtonHtml($processName)." ";
        }
         $customOperationButtons = $obj->getCustomOperationButtons();
        foreach($customOperationButtons as $buttonid =>$html)
        {   
            echo $html." ";
        }
    ?>
    <?php if($obj->isDelete()){?>
            <input type="button" Value="Delete" onClick="_delete()" />
    <?php }?>
    </td>
    <td align="right">
      <?php if($obj->isChoose()){?>
           <input type="button" Value="Keep Selected Rows" onClick="_keepRows()">
            <input type="button" Value="Exclude Selected Rows" onClick="_excludeRows()">

      <?php }?>
    </td>
    </table>
    </td>
</tr>

 <tr>
     <td>
      <style>
      <?php if(!$obj->isAllowWrap()){?>
          th, td { white-space: nowrap;}
        <?php }?>
           div.dataTables_wrapper {
                <?php if(!$obj->isFullScreenMode()) {?>
                 width: <?php echo $obj->getTableWidth()?>;
                <?php }?>
                margin: 0 auto;
              }

     </style>
     <div id="quickTableDiv"  <?php if(!$obj->isAllowWrap()){?>style="position:relative; margin:0px auto; padding:0px;overflow: auto;"<?php }?>>
    <table id="quickTable"  class="table table-striped  table-hover table-responsive" >
       <thead>    
       <tr>
     <?php       
     
          $titleinfo = $obj->getTitleInfo();
          $showTitle = $obj->isShowTitle();
          foreach($titleinfo as $dbname =>$title)
          {
                     
                 $titleName = $title["name"];    
          
             if($showTitle&&$title['ischecked']){

             
           
          
     ?>
     <td  align="center" width="<?php echo $title['width']?>" style="cursor:hand;<?php echo $title['style']?>">
             <?php echo htmlspecialchars_decode($titleName);?>
     </td>

     <?php }}?>
           </tr></thead><tbody>
     <?php 

          for($j=0;$j<$resultSize;$j++)
          {

            ?>
          <tr mainid="<?php echo $obj->getMainId($j)?>">
           <?php   
             foreach($titleinfo as $dbname =>$title)
             {
                 $titleName = $title["name"];      
                 $structure = $obj->getStructureByDbName($dbname);
                 $style = $structure['style'];
   
             ?>
          <td align="center" width="<?php echo $title['width']?>" style="<?php echo $style?>" dbname="<?php echo $dbname;?>">
         <?php echo htmlspecialchars_decode($obj->getValueByDbName($j,$dbname));?>
         </td>
         <?php }?>
        </tr>
  <?php }
  
  ?>
        
          </tbody>
     </table>
     </div>

    </td>
 </tr>  
 <?php $htmlArray = $obj->getButtomHtml();   
for($i=0;$i<count($htmlArray);$i++)
{
    $mName = $htmlArray[$i];    
?>
<tr><td align="right"><?php echo $obj->$mName($db,$_REQUEST);?></td></tr>

<?php }?>
 
     <?php      if($pageRows!=0&&$totalCount!=0)
     {?>
     
<tr>
     <td align="right"><font size="3"><?php if($curPage==1) {?>First|Previous<?php }else {?><a href="javascript:_jumpPage(1);"><font color="blue">First</font></a>|<a href="javascript:_jumpPage(<?php echo $curPage-1 ?>);"><font color="blue">Previous</font></a><?php }?>|<?php if($curPage==$totalPages) 
     {?>Next|Last<?php }else{?><a href="javascript:_jumpPage(<?php echo $curPage+1 ?>);"><font color="blue">Next</font></a>|<a href="javascript:_jumpPage(<?php echo $totalPages ?>);"><font color="blue">Last</font></a><?php }?>
         <input type="input" size="<?php echo strlen(strval($totalPages)) ?>" id="pagenum2" name="pagenum2" maxlength="<?php echo strlen(strval($totalPages)) ?>" value="" />  <input type="button" onclick="_changePage('pagenum2',<?php echo $totalPages?>)" value="Go"/></font>
     </td>
    </tr>

 <tr >
 <td align="right"> <font size="3"><?php echo $startRecord?>-<?php echo $endRecord?>/<?php echo $totalCount?> Records  <?php echo $curPage?>/<?php echo $totalPages?> Pages <input type="input" size="<?php echo strlen(strval($totalCount))+1 ?>" id="prows2" name="prows2" maxlength="<?php echo strlen(strval($totalCount))+1?>" onchange="_changePagerows('prows2')" value="<?php echo $pageRows?>"  /> Records/Page Page <?php echo $curPage ?></font></td>
 </tr>
 <?php } 
   else
     {
?>
 <tr> <td align="right"> <font size="3">  <?php echo $resultSize?> Records</font></td></tr>
<?php }?>
</table>
</form>
                </div>
              
       
    </div>
</body>
<script language="javascript">
     $('#quickTableDiv').perfectScrollbar();
      <?php if($obj->isFullScreenMode()) {?>
            $('#quickTableDiv').css('width',function(){return screen.width*0.95;});   
        <?php }?>
     </script>
<?php
if($qp_anchor!=null&&trim($qp_anchor)!="")
    {
        echo '<script language="javascript" >location.hash="'.$qp_anchor.'";</script>';
    } 
?>

    
<?php echo  $obj->getCustomJs();?></html>