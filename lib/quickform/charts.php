<?php 
set_time_limit(0);
   require_once(dirname(__FILE__)."/include.php");
   require_once($_SERVER['DOCUMENT_ROOT']."/vendor/autoload.php");
    use Quickplus\Lib\QuickFormConfig;
    use Quickplus\Lib\quickFormDrawer;
    use Quickplus\Lib\Tools\ArrayTools;
   $db =  new QuickFormConfig::$SqlType();
    $testing = 0;
    $formMark = ArrayTools::getValueFromArray($_REQUEST,'_statistics_formmark');
    $isreport =  ArrayTools::getValueFromArray($_REQUEST,'_statistics_isreport');
    $setname =  ArrayTools::getValueFromArray($_REQUEST,'_statistics_setname');
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
    //$form->setLoginCheck(false);
     if($form->initDb()!=null)
    {
            $db =$form->initDb();
    }
    $form->setAutoRefresh(false);
    $form->setCustomCol(false);
    $quickFormDrawer = new quickFormDrawer();
    $quickFormDrawer->setQuickForm($db,$form);
    $quickFormDrawer->setChartFilter($setname);
    $quickFormDrawer->setLoadTotalInfo(false);
    $obj = $quickFormDrawer->getForm($db,$_REQUEST,1,0,false,false);
     
    ?>
    <!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
    <html>
    <head>
    <meta http-equiv="Content-Type" content="text/html;charset=<?php echo QuickFormConfig::$encode?>" >
    <?php echo $obj->getScriptStr();?>
   </head>
   <body >
   <div  style="height: 100%;width:100%; position: absolute;"> 
     <?php if($obj->getSearchBar()){?>
    <div>
       <button class="btn btn-info " type="button"  onclick="$('#qp_searchBar').css('display','');"><span class="glyphicon glyphicon-search" aria-hidden="true"></span>Search</button>
      <div id="qp_searchBar" style="display:none;position:absolute;left:0%;top:0%;z-index:999" class="panel panel-info"><div class="panel-heading"><h4>Search</h4></div>  <div class="panel-body"><table width="100%"  >
         <?php $url = UrlTools::getFullUrl();?>
         <form name="quickForm" id="quickForm" action = "<?php echo $url;?>" method="post" enctype="multipart/form-data">
          <input type="hidden" id="curPage" name="curPage" value="1"/>
          <input type="hidden" id="searchSign" name="searchSign" value="1"/> 
          <input type="hidden" id="_statistics_formmark" name="_statistics_formmark" value="<?php echo $formMark;?>"/> 
          <input type="hidden" id="_statistics_isreport" name="_statistics_isreport" value="<?php echo $isreport;?>"/> 
          <input type="hidden" id="_statistics_setname" name="_statistics_setname" value="<?php echo $setname;?>"/> 
          <?php 
            $params = $_REQUEST;
            $params[$form->getSearchPrefix()."quick_Chart_Selecter"] = $setname;
          echo $quickFormDrawer->getSearchBarHtml($params);?>
         <tr><td colspan="2" align="right">
         
            <input type="button" onclick="$('#quickForm').submit();" value="Search"/>
             <button class="btn btn-danger " type="button"  onclick="$('#qp_searchBar').css('display','none');">Close</button>
          
           </td></tr></table>
        </form>
      </div></div></div>
        <?php }?>
     <div style="width:100%;height:100%;">
       
        <div align="center"> <?php echo $obj->getChartName($setname);?></div>
           <div align="center" style="width:100%;height:90%;"><?php echo $obj->getChartHtml($setname,$obj->getResult(),$_REQUEST);?> </div>
         <div align="center">   <input type="button"  class="btn btn-info  btn-xs" value="Close"  onclick="window.close();"/></div>
       
       </div>
   </body>
<?php echo  $obj->getCustomJs();?></html>
