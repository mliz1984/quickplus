<?php
use Quickplus\Lib\QuickTemplateDesigner;
use Quickplus\Lib\Tools\UrlTools;

    if($_GET['language']) $languageid = $_GET['language'];
    else $languageid = 1;
     
    $testing = 0;
?>
<?php
 
    $url = UrlTools::getFullUrl();
   
    $blank = $_REQUEST['blank'];
    $id = $_REQUEST['id'];
    $pageRows= $_REQUEST['pageRows'];
    if($pageRows==null||trim($pageRows)=="")
    {
        $pageRows = 20;
    }
    $page =  $_REQUEST['curPage'];
    $method =  $_REQUEST['method'];
    $searchSign =   $_REQUEST['searchSign'];
    $exportmode =  $_REQUEST['exportmode'];
    $qp_keeprowsids = $_REQUEST["qp_keeprowsids"];
    $qp_excluderowsids = $_REQUEST["qp_excluderowsids"];
    $qp_anchor = $_REQUEST["qp_anchor"];
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
     $quickForm = new QuickTemplateDesigner();
    $db = $quickForm->initDb();
    if($db==null)
    {
         $db = new QuickFormConfig::$SqlType();
    } 
    $quickFormDrawer->setQuickForm($db,$quickForm);
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
      
        if(!$methodResult)
        {
            $echo = $obj->getMethodWarning($method);
            if($echo==null||trim($echo)=="")
            {
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
<?php 
    $accountid  = $_SESSION[USER_REF]->id; 
    $mingroup   = getGroupmin($db, $currentpage);
    //echo "<script>alert('mingroup:".$mingroup."user_reff->groupid:".$_SESSION[USER_REF]->groupid."');</script>";

    if ($_SESSION[USER_REF]->groupid < $mingroup) echo "<script>alert('Access Denied!');window.location.href='./login.php';</script>";
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
<title>Welcome to :: KAYAS Networks</title>
<meta http-equiv="Content-Type" content="text/html;charset=<?php echo QuickFormConfig::$encode?>" >
<meta name="keywords" content="Kayas Networks, Kayas Communications, Kayas Shop, Telecommunication services, Consumer, Business, products, Phone Company, Software Development, Colocation & Bandwidth, Web Hosting, Domain Name" >
<meta name="description" content="Welcome to KayasNetworks! Take advantage of our expertise and of our service to access the great tool that is the World Wide Web. We believe that you can also benefit from our various promotions for Dial-up Access, High Speed and Cable Internet Access, and Web Hosting." >
<?php echo $obj->getScriptStr()?>
<?php echo QuickPage::getPageJs($obj,$searchSign,"quickTable");?>
</head>
<body >
<div id="master">
     <div id="content"> 
        <?php showAdminHead($db, $_SERVER['PHP_SELF']); ?>
        <div id="left"> 
            <?php
                showAdminMenu($db, $languageid, $_SESSION[USER_REF]->groupid, $_SERVER['PHP_SELF']);
                
            ?>         </div> <!-- e left -->          
             <div id="right">
                    <div id="texts">         
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
    if($_REQUEST["searchBarCollapseStatus"]!=null&&trim($_REQUEST["searchBarCollapseStatus "])!="")
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
 <button class="btn btn-info " type="button" data-toggle="collapse" data-target="#searchBarCollapse" aria-expanded="<?php echo strval($isCollapse);?>" aria-controls="searchBarCollapse">
<span class="glyphicon glyphicon-search" aria-hidden="true"></span> Search</button><br>
<div class="collapse <?php echo $collapse;?>" id="searchBarCollapse">
<input type="hidden" id="searchBarCollapseStatus" name="searchBarCollapseStatus" value="<?PHP echo strval($isCollapse); ?>"/>
      <table width="100%" >
         <?php 
            if($obj->getSearchBar())
            { 
                 $searchGroup = $obj->getSearchGroup();
                foreach($searchGroup as $groupid => $searchSetting)
                {
                    $groupName = $obj->getSearchGroupName($groupid);
              
            ?>
            <tr>
                <?php if($groupName!=null&&trim($groupName)!=""){?>
                 <td><?php echo $groupName;?>:</td>    
                 <?php }?><td>

                 <?php 
                    $count = count($searchSetting);
                    $count2 = 0;
                    echo '<div class="row">';
                    foreach($searchSetting as $dbname=>$setting){
                    $text = $setting["text"];
                    if($text!=null&&trim($text)!=""){
                      
                        $count2 += 2;
                     }
                    }
                    $width = intval((12-$count2)/$count);
                    foreach($searchSetting as $dbname=>$setting){

                    $text = $setting["text"];
                    if($text!=null&&trim($text)!=""){
                        echo '<div align="center" class="col-md-2">';
                        echo $text;
                        echo '</div>';
                     
                    }
                   echo '<div  class="col-md-'.$width.'">';
                   echo $quickFormDrawer->getSearchMode($dbname,$_REQUEST);
                   echo '</div>';
                   echo '</div>';
                 }?>
            </td></tr>
                
         <?php }}
         ?>    
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
                    </div>

            <?php }else{?>
                     <input type="button" onclick="_pageExport()" value="Export(Page)"/>
            <?php }}?>
             <?php   if($obj->getExport()&&$totalCount!=0) { 
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
            
             </td>
         </tr>     
     </table>
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
    <?php if($obj->isDelete()){?>
            <input type="button" Value="Delete" onClick="_delete()" />
    <?php }?>
     <?php 
        $customProcessMethod = $obj->getCustomProcessMethod();
        foreach($customProcessMethod as $processName =>$methodInfo)
        {   

            echo $obj->getProcessButtonHtml($processName)." ";
        }

    ?></td>
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
           $temp = substr($temp,1);
          
     
          for($j=0;$j<$resultSize;$j++)
          {

            ?>
          <tr>
           <?php   
             foreach($titleinfo as $dbname =>$title)
             {
                 $titleName = $title["name"];      
                 $structure = $obj->getStructureByDbName($dbname);
                 $style = $structure['style'];
   
             ?>
          <td align="center" width="<?php echo $title['width']?>" style="<?php echo $style?>" >
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
<tr><td align="right"><?php echo $obj->$mName($db);?></td></tr>

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
                <div id="footer">
                    <p class="style1">© KayasNetworks, 2009. All rights reserved.</p>
                </div><!--close footer-->   
          </div><!--close right-->
       </div><!--close content-->
    </div><!--close master-->
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
</html>