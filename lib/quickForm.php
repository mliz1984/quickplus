<?php
namespace  Quickplus\Lib;

use Quickplus\Lib\DataMsg\DataMsg;
use Quickplus\Lib\DataMsg\Data;
use Quickplus\Lib\Tools\DbTools;
use Quickplus\Lib\Tools\StringTools;
use Quickplus\Lib\Tools\HtmlElement;
use Quickplus\Lib\Tools\ArrayTools;
use Quickplus\Lib\Tools\CommonTools;
use Picqer\Barcode\BarcodeGeneratorSVG;
use Picqer\Barcode\BarcodeGenerator;
  class quickForm extends quickLayout
  {
        protected $debug = false;
        protected $jsOrderType = Array();
        protected $mainIdCol = null;
        protected $tables = Array();
        protected $editLinkTables = Array();
        protected $deleteLinkTables = Array();
        protected $deleteTables = Array();
        protected $isCustomTemplate = false;
        protected $isEdit = false;
        protected $isAdd = false;
        protected $isChoose = false;
        protected $isDelete = false;
        protected $isDetail = true;
        protected $detailName = "Detail";
        protected $checkboxid = "_checkbox_for_mainid_";
        protected $whereClause = "";
        protected $colRelation = null;
        protected $transfer = Array();
        protected $blank = false;
        protected $layoutData = Array();
        protected $keyMapping = Array();
        protected $isLayoutAdd = false;
        protected $isLayoutDelete = false;
        protected $isLayoutModify = false;
        protected $isLayoutCopy = false;
        protected $importTables = Array();
        protected $checkboxsForEdit = Array();
        protected $orderMethod = null;
        protected $searchMethod = null;
        protected $colDetail = Array();
        protected $validateFromDb = true;
        protected $filteMap = Array();
        protected $editDefaultValueFromDb = true;
        protected $customDataMethod = null;
        protected $colVisGroup = Array();
        protected $statisticsGroup = Array();
        protected $chartGroup = Array();
        protected $dashboardGroup = Array();
        protected $layoutColInfo = Array();
        protected $uploadMaxSize = Array();
        protected $showTitle = true;
        protected $showTableButton = true;
        protected $isTemplate = false;
        protected $primaryKeyValueArray = Array();
        protected $customTemplateButton = Array();
        protected $customCol = null;
        protected $customColMark = "quickform_column_visibility";
        protected $customColText = null;
        protected $autoRefresh = true;
        protected $autoRefreshTime = 0;
        protected $autoRefreshTimeText = null;
        protected $autoRefreshMark = "quickform_auto_refresh";
        protected $afterQuickUpdateMethod = Array();
        protected $chooseCheckBoxMethod = "getCheckBoxForMainId";
        protected $chooseCheckBoxDbName = null;
        protected $quickEditDefaultText = Array();
        protected $searchExtendHtml = null;
        protected $seqTitle = "Seq.";
        protected $chartFilterSetting = Array();
        protected $dashboardFilterSetting = Array();
        protected $statisticFilterSetting = Array();
        protected $statisticDataProcessMethod = Array();
        protected $chartDataProcessMethod = Array();
        protected $colVisable = Array();
        protected $joinField = Array();
        protected $relationDataMethod = Array();
        protected $relationData = Array();
        protected $subForm = Array();
        protected $barCodeMapping = Array();
        protected $joinArray = Array();
        protected $reportHead = null;
        protected $max_row_in_dashboard = null;
        public function setMaxRowInDashboard($max_row_in_dashboard)
        {
                $this->max_row_in_dashboard = $max_row_in_dashboard;
        }
        public function getMaxRowInDashboard()
        {
             $maxRowInDashboard = $this->max_row_in_dashboard;
             if(!empty($maxRowInDashboard))
             {
                $maxRowInDashboard = intval(QuickFormConfig::$max_row_in_dashboard);
             }
             return $maxRowInDashboard;
        } 
        public function setBarCodeMapping($dbname,$colname)
        {
            $this->barCodeMapping[$dbname] = $colname;
        }
        public function getBarCodeMapping($dbname)
        {
            $ret = $dbname;
            if(isset($this->barCodeMapping[$dbname])&&$this->barCodeMapping[$dbname]!=null&&trim($this->barCodeMapping[$dbname])!="")
            {
                $ret = $this->barCodeMapping[$dbname];
            }
            return $ret;
        }
        public function getSubForm($id)
        {
            $ret = null;

            if(is_array($this->subForm[$id]))
            {
                $ret =  $this->subForm[$id];
            }
            return $ret;
        }
        public function initSubForm()
        {

        }
        public function setSubForm($id,$classsrc,$classname)
        {
            $this->subForm[$id] = Array("classsrc"=>$classsrc,"classname"=>$classname);
        }
        public function getRelationData($key)
        {
            return $this->relationData[$key];
        }
        public function setRelationDataMethod($key,$methodName,$dbnames)
        {
            $this->relationDataMethod[$key] = Array("dbnames"=>$dbnames,"methodName"=>$methodName);
        } 
        public function runRelationDataMethod($db, $src = null) 
        {
            foreach($this->relationDataMethod as $k =>$v)
            {
                $methodName =$v["methodName"];
                $dbnames = $v["dbnames"];
                $array = explode(",", $dbnames);
                $run = false;
                foreach($array as $a)
                {
                   if($this->getColStatus($a))
                   {
                      $run = true;
                      break;
                   }
                }
                if($run)
                {
                    $this->relationData[$k] = $this->$methodName($db,$src);
                }
            }
        }
        public function getJoinField()
        {
            return $this->joinField;
        }
        public function setColVisable($dbname,$visable)
        {
            $this->colVisable[$dbname] = $visable;
        }
        public function getColVisable($dbname)
        {
            $result =true;
            if(isset($this->colVisable[$dbname])&&is_bool($this->colVisable[$dbname])&&!$this->colVisable[$dbname])
            {
                $result = false;
            }
            return $result;

        }
        public function setStatisticDataProcessMethod($setname,$method)
        {
            $this->statisticDataProcessMethod[$setname] = $method;
        }
        public function getStatisticDataProcessMethod($setname)
        {
            $ret = null;
            if(isset($this->statisticDataProcessMethod[$setname])&&$this->statisticDataProcessMethod[$setname]!=null&&trim($this->statisticDataProcessMethod[$setname])!="")
            {

                $ret  = trim($this->statisticDataProcessMethod[$setname]);
            }
            return $ret;
        }
        protected function processStatisticData($oriArray,$setname,$src)
        {
            
            $method = $this->getStatisticDataProcessMethod($setname);
            
            if($method!=null&&trim($method)!=null)
            {
                
                $oriArray = $this->$method($setname,$oriArray,$src);
            }
            return $oriArray;
        }
        public function initDb()
        {
            return null;
        }
        public function getTimeZoneDiff($isAbs=false)
        {
                $dateUtil = new DateUtil();
                $result =  $dateUtil->getTimeZoneDiff();
                if($isAbs)
                {
                    $result = abs($result);
                }
                return $result;
        }
         public function setDashboardFilter($dashboardid,$dbname,$search=null,$defaultsearch=null,$oridbname=null,$isSql=true,$having=false,$groupid=null,$relation="AND",$text="")
        {
            $this->dashboardFilterSetting[$dashboardid]["filter"][$dbname] = Array("dbname"=>$dbname,"search"=>$search,"defaultsearch"=>$defaultsearch,"oridbname"=>$oridbname,"isSql"=>$isSql,"having"=>$having,"groupid"=>$groupid,"relation"=>$relation,"text"=>$text) ;
        }
        public function setChartFilter($chartId,$dbname,$search=null,$defaultsearch=null,$oridbname=null,$isSql=true,$having=false,$groupid=null,$relation="AND",$text="")
        {
            $this->chartFilterSetting[$chartId]["filter"][$dbname] = Array("dbname"=>$dbname,"search"=>$search,"defaultsearch"=>$defaultsearch,"oridbname"=>$oridbname,"isSql"=>$isSql,"having"=>$having,"groupid"=>$groupid,"relation"=>$relation,"text"=>$text) ;
        }
        public function setStatisticFilter($setname,$dbname,$search=null,$defaultsearch=null,$oridbname=null,$isSql=true,$having=false,$groupid=null,$relation="AND",$text="")
        {
            $this->statisticFilterSetting[$setname]["filter"][$dbname] = Array("dbname"=>$dbname,"search"=>$search,"defaultsearch"=>$defaultsearch,"oridbname"=>$oridbname,"isSql"=>$isSql,"having"=>$having,"groupid"=>$groupid,"relation"=>$relation,"text"=>$text) ;
        }
     
 
        public function initChartFilter($chartId)
        {  
            if(is_array($this->chartFilterSetting[$chartId]))
            {
               
                $array = $this->chartFilterSetting[$chartId];
                if(isset($array["filter"])&&is_array($array["filter"])&&count($array["filter"])>0)
                {
                    foreach($array["filter"] as $dbname => $f)
                    {
                        $this->setSearchFieldType($f["dbname"],$f["search"],$f["defaultsearch"],$f["oridbname"],$f["isSql"],$f["having"],$f["groupid"],$f["relation"],$f["text"]);
                    }
                }
            }
            $chartGroup = $this->getChartGroup();
            if(is_array($chartGroup)&&count($chartGroup)>1)
            {
                $this->addField("quick_Chart_Selecter","Chart Type");
                $this->setSearchFieldType("quick_Chart_Selecter","chartSelecter",$chartid);
            }

            return $this;
        }

        public function initDashboardFilter($dashboardId)
        {  
            if(is_array($this->dashboardFilterSetting[$dashboardId]))
            {
               
                $array = $this->dashboardFilterSetting[$dashboardId];
                if(isset($array["filter"])&&is_array($array["filter"])&&count($array["filter"])>0)
                {
                    foreach($array["filter"] as $dbname => $f)
                    {
                        $this->setSearchFieldType($f["dbname"],$f["search"],$f["defaultsearch"],$f["oridbname"],$f["isSql"],$f["having"],$f["groupid"],$f["relation"],$f["text"]);
                    }
                }
            }
            $dashboardGroup = $this->getDashboardGroup();
            if(is_array($dashboardGroup)&&count($dashboardGroup)>1)
            {
                $this->addField("quick_Dashboard_Selecter","Dashboard Type");
                $this->setSearchFieldType("quick_Dashboard_Selecter","dashboardSelecter",$dashboardId);
            }

            return $this;
        }

        public function dashboardSelecter($dbname,$colname,$src,$sql=false,$defaultValue="")
        {
            if($sql)
            {
                return "";
            }
            else
            {
                $dashboardGroup = $this->getDashboardGroup();
                $array = Array();
                foreach($chartGroup as $chartid => $chartArray)
                {
                    $array[$chartid] = $chartArray["name"];
                }
                $sign = $this->getSearchPrefix().$dbname;  
                $html = new HtmlElement($sign,$sign);
                $html->setFunction("onChange","$('#_statistics_setname').val($(this).val());");
                return $html->getSelect($array,$defaultValue,true);
            }
        }   
        
        public function chartSelecter($dbname,$colname,$src,$sql=false,$defaultValue="")
        {
            if($sql)
            {
                return "";
            }
            else
            {
                $chartGroup = $this->getChartGroup();
                $array = Array();
                foreach($chartGroup as $chartid => $chartArray)
                {
                    $array[$chartid] = $chartArray["name"];
                }
                $sign = $this->getSearchPrefix().$dbname;  
                $html = new HtmlElement($sign,$sign);
                $html->setFunction("onChange","$('#_statistics_setname').val($(this).val());");
                return $html->getSelect($array,$defaultValue,true);
            }
        }   

        public function statisticSelecter($dbname,$colname,$src,$sql=false,$defaultValue="")
        {
            if($sql)
            {
                return "";
            }
            else
            {
                $statisticsGroup = $this->getStatisticsGroup();
                $array = Array();
                foreach($statisticsGroup as $setname => $statisticsGroupArray)
                {
                    $array[$setname] = $statisticsGroupArray["name"];
                }
                $sign = $this->getSearchPrefix().$dbname;  
                $html = new HtmlElement($sign,$sign);
                $html->setFunction("onChange","$('#_statistics_setname').val($(this).val());");
                return $html->getSelect($array,$defaultValue,true);
            }
        }   

        public function initStatisticFilter($setname)
        {
            if(is_array($this->statisticFilterSetting[$setname]))
            {
                $array = $this->statisticFilterSetting[$setname];
                if(isset($array["filter"])&&is_array($array["filter"])&&count($array["filter"])>0)
                {
                    foreach($array["filter"] as $dbname => $f)
                    {
                        $this->setSearchFieldType($f["dbname"],$f["search"],$f["defaultsearch"],$f["oridbname"],$f["isSql"],$f["having"],$f["groupid"],$f["relation"],$f["text"]);
                    }
                }
            }
            $statisticsGroup = $this->getStatisticsGroup();
            if(is_array($statisticsGroup)&&count($statisticsGroup)>1)
            {
                $this->addField("quick_Statistic_Selecter","Statistic Type");
                $this->setSearchFieldType("quick_Statistic_Selecter","statisticSelecter",$setname);
            }
            return $this;
        }



        
        
        public function setSeqTitle($seqTitle)
        {
            $this->seqTitle = $seqTitle;
        }
        public function getSeqTitle()
        {
            return $this->seqTitle;
        }
        public function setAutoRefreshMark($autoRefreshMark)
        {
            $this->autoRefreshMark = $autoRefreshMark;
        }
        public function getAutoRefreshMark()
        {
            return $this->autoRefreshMark;
        }
        public function setAutoRefresh($autoRefresh)
        {
            $this->autoRefresh = $autoRefresh;
        }
        public function getAutoRefresh()
        {
            return $this->autoRefresh;
        }
        public function setAutoRefreshTime($autoRefreshTime)
        {
            $this->autoRefreshTime = floatval($autoRefreshTime);
        }
        public function getAutoRefreshTime($src)
        {
            $result =  floatval($this->autoRefreshTime);    
            if(is_array($src))
            { 
                $mark = $this->getSearchPrefix().$this->autoRefreshMark;
                if(isset($src[$mark])&&floatval($src[$mark])>0)
                {
                    $result = floatval($src[$mark]);
                }          
            }
            return $result;
        }
        public function setAutoRefreshTimeText($autoRefreshTimeText)
        {
            $this->autoRefreshTimeText = $autoRefreshTimeText;
        }
        public function getAutoRefreshTimeText($src)
        {
            $result = "Auto Refresh Time";
            $mark = ArrayTools::getValueFromArray($src,$this->getSearchPrefix().$this->autoRefreshMark);
            if($this->autoRefreshTimeText!=null&&trim($this->autoRefreshTimeText)!="")
            {
                $result = $this->autoRefreshTimeText;
            }
            else if(QuickFormConfig::$autoRefreshTimeText!=null&&trim(QuickFormConfig::$autoRefreshTimeText)!="")
            {
                $result = QuickFormConfig::$autoRefreshTimeText;
            }
            $result = StringTools::conv($result,QuickFormConfig::$encode);
            return $result;
        }
        public function initAutoRefresh($src)
        {
            if($this->autoRefresh)
            {      
                $defaultValue = 0;
                $mark = ArrayTools::getValueFromArray($src,$this->getSearchPrefix().$this->autoRefreshMark);
                if(isset($src[$mark])&&floatval($src[$mark])>0)
                {
                    $defaultValue = floatval($src[$mark]);
                }
                $this->addField($this->autoRefreshMark,$this->getAutoRefreshTimeText($src));
                $this->setSearchFieldType($this->autoRefreshMark,"defaultSearchShowMode",$defaultValue);
            }
        }
        public function setSearchExtendHtml($searchExtendHtml)
        {
            $this->searchExtendHtml = $searchExtendHtml;
        }
        public function getSearchExtendHtml()
        {
            return $this->searchExtendHtml;
        }
        public function setQuickEditDefaultTxet($dbname,$quickEditDefaultText)
        {
            $this->quickEditDefaultText[$dbname] = $quickEditDefaultText;
        }
        public function setChooseCheckBoxDbName($chooseCheckBoxDbName)
        {
            $this->chooseCheckBoxDbName = $chooseCheckBoxDbName;
        }
        public function getChooseCheckBoxDbName()
        {
            return $this->chooseCheckBoxDbName;
        }
        public function setChooseCheckBoxMethod($chooseBoxMethod)
        {
            $this->chooseCheckBoxMethod = $chooseCheckBoxMethod;
        }
        public function getChooseCheckBoxMethod()
        {
            return $this->chooseCheckBoxMethod;
        }
        public function setAfterQuickUpdateMethod($dbname,$method)
        {
            $this->afterQuickUpdateMethod[$dbname] = $method;
        }
        public function getAfterQuickUpdateMethod($dbname)
        {
            return $this->afterQuickUpdateMethod[$dbname];
        }
        public function setCustomColText($customColText)
        {
            $this->customColText = $customColText;
        }
        public function getCustomColText()
        {
            $result = "Column Visibility";
            if($this->customColText!=null&&trim($this->customColText)!="")
            {
                $result = $this->customColText;
            }
            else if(QuickFormConfig::$customColText!=null&&trim(QuickFormConfig::$customColText)!="")
            {
                $result = QuickFormConfig::$customColText;
            }
            $result = StringTools::conv($result,QuickFormConfig::$encode);
            return $result;
        }
        public function setCustomColMark($customColMark)
        {
            $this->customColMark = $customColMark;
        }
        public function getCustomColMark()
        {
            return $this->customColMark;
        }
        public function initCustomCol($src)
        {
            $colSetting = null;
            if(isset($src[$this->getSearchPrefix().$this->customColMark]))
            {
                $colSetting = $src[$this->getSearchPrefix().$this->customColMark];
            }
            $fields = $this->getReportField(true);
            $defaultValue = "";
            if(is_array($colSetting))
            {

                foreach($fields as $dbname =>$info)
                {
                   $this->setColStatus($dbname,in_array($dbname,$colSetting));
                }
            }
            else
            {
                $colSetting = Array();
                foreach($fields as $dbname =>$info)
                {
                    $choosed = $this->getColVisable($dbname);

                    if($this->getColStatus($dbname)&& $choosed)
                    {
                        $defaultValue .= ",".$dbname;
                    }
                }
                $defaultValue = ltrim($defaultValue,",");
            
            }
            if($this->getCustomCol())
            { 
               
                $array = Array();

                foreach($fields as $dbname =>$info)
                {
                    $title = $this->fields[$dbname]["displayname"];
                    $array[$dbname] = $title;
                }
                $this->addAttachDataByMap($this->getCustomColMark(),$array);
                $customColMark = $this->getCustomColMark();
                $this->addField($customColMark,$this->getCustomColText());
                if(count($this->reportField)>1)
                {
                    $this->setSearchFieldType($customColMark,"getCheckboxesBy".$customColMark,$defaultValue);
                }
            }
        }
        public function setCustomCol($customCol)
        {
            $this->customCol = $customCol;
        }
        public function getCustomCol()
        {
            $result = false;
            if(is_bool($this->customCol))
            {
                $result = $this->customCol;
            }
            else if(is_bool(QuickFormConfig::$customCol))
            {
                $result = QuickFormConfig::$customCol;
            }
            return $result;
        }
        public function getTemplateStrByDbname($dbname)
        {
            return "{!--".$dbname."--!}";
        }
        public function setCustomTemplateButton($key,$name,$value)
        {
            $this->customTemplateButton[$key] = Array("name"=>$name,"value"=>$value); 
        }
        public function getCustomTemplateButtonArray()
        {
            return $this->customTemplateButton;
        }
        public function getPrimaryKeyValue($tableSign)
        {
            return $this->primaryKeyValueArray[$tableSign];
        }
        public function setIsTemplate($isTemplate)
        {
            $this->isTemplate = $isTemplate;
        }
        public function isTemplate()
        {
            return  $this->isTemplate;
        }
        public function isShowTableButton()
        {
            return $this->showTableButton;
        }
        public function setShowTableButton($showTableButton)
        {
            $this->showTableButton = $showTableButton;
        }
        public function isShowTitle()
        {
            return $this->showTitle;
        }
        public function setShowTitle($showTitle)
        {
            $this->showTitle = $showTitle;
        }

        public function setLayoutColInfo($dbname,$initMethod,$isExport=false)
        {
            $type = "report";
            if($isExport)
            {
                $type = "export";
            }
            $this->layoutColInfo[$dbname][$type] = $initMethod;
        }
        
        public function getLayoutColInfo($dbname)
        {
            $isExport = $this->isExportMode();
             $type = "report";
            if($isExport)
            {
                $type = "export";
            }
            return $this->layoutColInfo[$dbname][$type];
        }
        public function setStatisticCommonTranslateDataByAttachData($col,$key)
        {
             $array =  $this->getAttachData($key);
             $data = Array();
             foreach($array as $a)
             {
                $k = $a["attachdata_id"];
                $v = $a["attachdata_name"];
                $data[$k] = $v;
             }
             $this->setStatisticCommonTranslateData($col,$data);
        }
        public function setChartCommonTranslateDataByAttachData($col,$key)
        {
            $this->setStatisticCommonTranslateDataByAttachData($col,$key);
        }
        public function setChartTranslateDataByAttachData($setname,$col,$key)
        {
            $this->setStatisticTranslateDataByAttachData($setname,$col,$key);
        }
        public function setStatisticTranslateDataByAttachData($setname,$col,$key)
        {
             $array =  $this->getAttachData($key);
             $data = Array();
             foreach($array as $a)
             {
                $k = $a["attachdata_id"];
                $v = $a["attachdata_name"];
                $data[strval($k)] = strval($v);
             }
             $this->setStatisticTranslateData($setname,$col,$data);
        }
        public function getChartName($chartid)
        {

            return   $this->chartGroup[$chartid]["name"];
        }
        public function getStatisticName($setname)
        {
             return   $this->statisticsGroup[$setname]["name"];
        }
        public function  setSimpleStatistic($setname,$name,$methodname,$cols,$spiltBy=",")
        {
            $this->statisticsGroup[$setname] = Array("setname"=>$setname,"name"=>$name,"methodname"=>$methodname,"cols"=>$cols,"spiltBy"=>$spiltBy,"multiStatistic"=>false);
        }

        public function setStatistic($setname,$name,$cols,$spiltBy=",")
        {
             $this->statisticsGroup[$setname] = Array("setname"=>$setname,"name"=>$name,"cols"=>$cols,"spiltBy"=>$spiltBy,"multiStatistic"=>true);
        }

        public function setSimpleChart($charttype,$chartid,$name,$serieName,$methodname,$cols,$spiltBy=",")
        {
            $chartid = $chartid;
            $colsArray = explode($spiltBy,$cols);
            $ycol = null;
            $xcol = null;
            if(count($colsArray)>0)
            {
                $xcol = $colsArray[0];
                if(count($colsArray)>1)
                {
                     $ycolsArray = array_splice($colsArray, 1);
                     $ycol = implode($spiltBy,$ycolsArray);
                }
            }
            $this->chartGroup[$chartid] = Array("chartid"=>$chartid,"name"=>$name);
          //  echo $xcol."--".$ycol;
            $this->setChartInfo($charttype,$chartid,$xcol);
         //   $this->setChartSerie($chartid,$serieName,$methodname,$ycol);
          
        }
        public function setDashboard($dashboardid,$name)
        {
             $this->dashboardGroup[$dashboardid] = Array("name"=>$name);
        }
        public function addChartToDashboard($dashboardid,$chartid,$rowid,$colid,$groupid=null,$width=null,$height=null)
        {
            $this->dashboardGroup[$dashboardid]["content"][$rowid][$colid] = Array("type"=>"chart","id"=>$chartid,"groupid"=>$groupid,"width"=>$width,"height"=>$height);
        }
        public function addStatisticToDashboard($dashboardid,$statisticid,$rowid,$colid,$groupid=null,$width=null,$height=null)
        {
            $this->dashboardGroup[$dashboardid]["content"][$rowid][$colid] = Array("type"=>"statistic","id"=>$statisticid,"groupid"=>$groupid,"width"=>$width,"height"=>$height);
        }

        public function getDashboardHtml($dashboardid,$src)
        {
            $ret = "";
            if(is_array($this->dashboardGroup[$dashboardid]["content"]))
            {
                $array = $this->dashboardGroup[$dashboardid]["content"];
                $j  = count($array);
                $max_row_in_dashboard = $this->getMaxRowInDashboard();
                if($max_row_in_dashboard>0&&$j>$max_row_in_dashboard)
                {
                    $j = $max_row_in_dashboard;
                }
                foreach($array as $rowid=>$arr)
                {
                    $i = count($arr);
                    foreach($arr as $colid=>$data)
                    {
                        $type =$data["type"];
                        $id = $data["id"];
                        $groupid = $data["groupid"];
                        $width = $data["width"];
                        $height = $data["height"];
                        if($type=="chart")
                        {
                            $this->setChartWidth($id,"95%");
                            if(!empty($height))
                            {
                                    $height = intval($this->getChartHeight($id)/$j);
                            }
                            $this->setChartHeight($id,$height."px");
                            $html = $this->getChartHtml($id,$this->getResult(),$src);
                        }
                        else
                        {
                            $html = $this->getStatisticsHtml($id,$this->getResult(),$src);
                        }
                        $this->setColByHtml($rowid,$colid,$html, $groupid,$width);
                    }
                }
                $ret = $this->getHtml();
            }
            return $ret;
        }
        public function setChart($charttype,$chartid,$name,$xcol)
        {
             $this->chartGroup[$chartid] = Array("chartid"=>$chartid,"name"=>$name);
             $this->setChartInfo($charttype,$chartid,$xcol);
        }

        public function getChartGroup()
        {
            return $this->chartGroup;
        }

        public function getDashboardGroup()
        {
            return $this->dashboardGroup;
        }

        public function getStatisticsGroup()
        {
            return $this->statisticsGroup;
        }

        protected function getChartsScriptByChartid($chartid)
        {    
            $result = "";
            $array = $this->chartGroup[$chartid];
            if(is_array($array)&&count($array)>0)
            {
                $result.="  {
                                 text: '".$array["name"]."',
                                 action: function ( e, dt, node, config ) { _showChart('".$array["chartid"]."');}
                            }";

            }
            return $result;
        }

        public function getChartsScript()
        {
            $tmp = "";
            $array = $this->getChartGroup();

            if(is_array($array)&&count($array)>0)
            {
                foreach($array as $chartid=> $value)
                {
                    $tmp.=$this->getChartsScriptByChartid($chartid).",";
                }   
            }
            $tmp = trim($tmp,",");
            $html = "";
            if(trim($tmp!=""))
            {
                $html.="  {
                                extend: 'collection',
                                text: 'Charts',
                                buttons: [".$tmp."]
                          },";
            }
            return $html;
        }
        
        public function getStatisticsScript()
        {
            $tmp = "";
            $array = $this->getStatisticsGroup();

            if(is_array($array)&&count($array)>0)
            {
                foreach($array as $setname=> $value)
                {
                    $tmp.=$this->getStatisticsScriptBySetName($setname).",";

                }   
            }
            $tmp = trim($tmp,",");
            $html = "";
            if(trim($tmp!=""))
            {
                $html.="  {
                                extend: 'collection',
                                text: 'Statistics',
                                buttons: [".$tmp."]
                          },";
            }
            return $html;
        }

        protected function getStatisticsScriptBySetName($setname)
        {    
            $result = "";
            $array = $this->statisticsGroup[$setname];
            if(is_array($array)&&count($array)>0)
            {
                $result.="  {
                                text: '".$array["name"]."',
                                 action: function ( e, dt, node, config ) { _showStat('".$array["setname"]."');}
                            }";
            }
            return $result;
        }

        public function setColVisGroup($name,$obj,$spiltby=",")
         {  
            if(!is_array($obj))
            {
                $obj = explode($spiltby, $obj);

            }
            $this->colVisGroup[$name] = $obj;
         }
         public function getTemplateScript()
         { 
           $html = "";
           if($this->isTemplate())
           {
               $url = QuickFormConfig::$quickFormBasePath."quickTemplate.php?classname=".get_class($this);
               $html = " {text: 'Template',action: function ( e, dt, node, config ) {window.open('". $url."','');} },";
           }
           return $html;
         }
         public function getHelpScript()
         {
            $html = " {text: 'Help',action: function ( e, dt, node, config ) {}},";
            //return $html;
            return "";
         }
         public function getColVisScript()
         {
            $tmp = "";
            $array = $this->colVisGroup;
            if(is_array($array)&&count($array)>0)
            {
                foreach($array as $name=> $value)
                {
                    $tmp.=$this->getColVisScriptByName($name).",";

                }   
            }
            $tmp = trim($tmp,",");
            $html = "";
            if(trim($tmp!=""))
            {
                $html.="  {
                                extend: 'collection',
                                text: 'Column Groups',
                                buttons: [".$tmp."]
                          },";
            }
            return $html;

         }
         protected function getColVisScriptByName($name)
         {  
            $result = "";
            $array = $this->colVisGroup[$name];
            $titleinfo = $this->getTitleInfo();
            $show = "";
            $hide = "";
            if(is_array($array)&&count($array)>0)
            {   
                $i = 0;
                foreach($titleinfo as $key => $value)
                {
                    if($i==0||($i==1&&$this->isChoose())||in_array($key,$array))
                    {
                        $show.=",".$i;
                    }
                    else
                    {
                        $hide .=",".$i;
                    }
                    $i++;
                }
                $show= trim($show,",");
                $hide= trim($hide,",");
            }
            $result .="{
                            extend: 'colvisGroup',
                            text: '".$name."',
                            show: [".$show."],
                            hide: [".$hide."]
                       }";
            return $result;
         }

        public function setCustomDataMethod($customDataMethod)
        {
            $this->customDataMethod = $customDataMethod;
        }
        
        public function getCustomDataMethod()
        {
            return $this->customDataMethod;
        }

        public function isCustomTemplate()
        {
            return $this->isCustomTemplate;
        }
        public function setIsCustomTemplate($isCustomTemplate)
        {
            $this->isCustomTemplate = $isCustomTemplate;
        }
        public function setEditLinkTable($linkTable)
        {
            if(isset($this->linkInfo[$linkTable])&&!in_array($linkTable,$this->editLinkTables))
            {
                $this->editLinkTables[] = $linkTable;
            }
        }
         public function setDeleteLinkTable($linkTable)
        {
            if(!in_array($linkTable,$this->deleteLinkTables))
            {
                $this->deleteLinkTables[] = $linkTable;
            }
        }
        public function getEditLinkTables()
        {
            return $this->editLinkTables;
        }
        public function getDeleteLinkTables()
        {
            return $this->deleteLinkTables;
        }
        public function modifySqlForLinkData($linkDataSign)
        {
            $linked = Array();
            if(count($linkDataSign)>0)
            {
                $sql = $this->getSql();
                foreach($linkDataSign as $dataSign)
                {
                    $path = $this->linkPath[$dataSign]["path"];
                    if($path==null)
                    {
                        if(!in_array($dataSign, $linked))
                        {
                            
                            $linkdbname = $this->linkPath[$dataSign]["srcdbname"];
                            $colname = $this->getOriDbName($linkdbname);
                            $linkkey = $this->linkInfo[$dataSign]["linkkey"];
                            $tablename = $this->linkInfo[$dataSign]["tablename"];
                            $sql .= " LEFT JOIN ".$tablename." ".$dataSign." ON ".$colname." = ".$dataSign.".".$linkkey." "; 
                            $linked[] = $dataSign;
                        }
                    }
                    else
                    {
                        foreach($path as $p)
                        {
                            if(!in_array($p, $linked))
                            { 
                               
                                $tablename = $this->linkInfo[$p]["tablename"];
                                $linkkey = $this->linkInfo[$p]["linkkey"];
                                $linkdbname = $this->linkInfo[$p]["linkdbname"];
                                $linkdbField = $this->fields[$linkdbname];
                                $colname = "";
                                if($linkdbField["islinkfield"])
                                {
                                    $colname = $linkdbField["linkdatasign"].".".$linkdbField["linkkey"];
                                }
                                else
                                {
                                    $colname = $this->getOriDbName($linkdbname);
                                }
                                $sql .= " LEFT JOIN ".$tablename." ".$p." ON ".$colname." = ".$p.".".$linkkey." "; 
                                $linked[] = $dataSign;
                            }
                        }
                    }
                }
                $this->setSql($sql,false);
                $mainIdColInfo  =$this->getMainIdCol(); 
                $mainIdCol = $mainIdColInfo["dbname"];
                $this->setGroupFieldType($mainIdCol);
            }
        }

        public function getSqlByTableName($db,$tablename)
        {
            $data = new Data($db,$tablename);
            $this->sql = $data->getSearchSql();
        }
        public function getHtmlArrayForColMapping($type,$data,$keyValue=null,$mainid=null)
        {
            $result = Array();

            foreach($data as $key=>$colData)
            {
                $dbname = $key;
                $isEditField = false;
                $value = $colData;

                $method = null;
                $html = null;
                if(is_array($colData))
                {
                    $value = $colData["value"];
                    if(isset($colData["ishtml"])&&$colData["ishtml"])
                    {
                        $html = $colData["value"];
                    }
                    else if(isset($colData["method"])&&$colData["method"]!=null&&trim($colData["method"])!=null)
                    {
                        $method = $colData["method"];
                    }
                }
                if(StringTools::isStartWith($key,$this->getEditPrefix()))
                {
                    $isEditField = true;
                    $dbname = substr($key,strlen($this->getEditPrefix()));
                }
                if($html!=null)
                {
                    $array["html"][$key] = $html;
                }
                else
                {
                    if($type=="edit")
                    {
                        if($isEditField&&$this->editField[$dbname]!=null)
                        {
                            $field = $this->editField[$dbname];
                            if($method==null)
                            {
                                $method = $field["method"];
                            }
                            $html = $this->showEditShowMode($method,$field["save"],$field["upload"],$dbname,null,$value,true);
                            $array["html"][$key] = $html;
                        }
                        else if($this->editCustomField[$dbname]!=null)
                        {   
                              $html = $this->showEditCustomShowModeById($dbname,true,$value,$method);
                              $array["html"][$key] = $html;
                        }

                        $array["validate"] = $this->getValidateScript(null,false);
                    }
                    else if($type=="quickEdit")
                    {
                         $html = $this->getQuickEditShowModeHtml($mainid,$keyValue,$this->getQuickEditPrefix(),$dbname,$value,$method,false,true);
                         $array["html"][$key] = $html;
                    } 

                }
            }
            return $array;
        }

        public function getFilteMap()
        {
            return $this->filteMap;
        }
        public function setValidateFromDb($validateFromDb)
        {
            $this->validateFromDb = $validateFromDb;
        } 
        public function getValidateFromDb()
        {
            return $this->validateFromDb;
        } 
        public function setEditDefaultValueFromDb($editDefaultValueFromDb)
        {
            $this->editDefaultValueFromDb = $editDefaultValueFromDb;
        } 
        public function getEditDefaultValueFromDb()
        {
            return $this->editDefaultValueFromDb;
        } 
        public function loadEditDefaultValueFromDb()
        {
            $editField =  $this->getEditField();
            foreach($editField as $dbname => $info)
            {
                $colDetail = $this->colDetail[$dbname];
                if(is_array($colDetail))
                {
                    $value = $colDetail["default"];
                    if($value!=null&&trim($value)!=""&&$this->getEditDefaultValue($dbname)==null)
                    {
                        $this->setEditDefaultValue($dbname,$value);
                    }
                    else 
                    {
                        $value = ArrayTools::getValueFromArray($colDetail,"COLUMN_DEF");
                        if($value!=null&&trim($value)!="")
                        {
                            $value= substr(2, strlen($value)-4);
                            $this->setEditDefaultValue($dbname,$value);
                        }
                    }
                }
            }
        }
         public function getExtendTableHtml($extendTableId,$dataArray)
         {
             $html =null;
             if(is_array($this->extendTable[$extendTableId])&&is_array($this->extendTableCol[$extendTableId])&&count($this->extendTableCol[$extendTableId])>0)
             { 
                
                $mustHaveColNum =  $this->extendTable[$extendTableId]["musthavecolnum"];
                $initColNum = $this->extendTable[$extendTableId]["initcolnum"];
                $qet = new QuickExtendTable();
                $qet->setExtendTablePrefix($this->getExtendTablePrefix());
                $this->loadValidateFromDb(true,$extendTableId);

                foreach($this->extendTableCol[$extendTableId] as $dbname =>$colinfo)
                {   
                  
                    $js.=$this->extendTableCol[$extendTableId][$dbname]["initjs"];
                    $colHtml = "''";
                    $title = $colinfo["title"];
                    $addjs = $colinfo["addjs"];
                    $isRequired = $this->getExtendValidateRule($extendTableId,$dbname,"required");
                    if(is_bool($isRequired)&&$isRequired)
                    {
                        $title .= " (*)";
                    }
                    if($colinfo["ishtml"])
                    {
                        $colHtml = $colinfo["custommethod"];
                    }
                    else
                    {
                        $method = $colinfo["custommethod"];
                        if($method!=null&&trim($method)!="")
                        {
                            $searchPrefix = $this->getSearchPrefix();
                            $qettp = "qettp_";
                            $this->setSearchPrefix($qettp);
                            $colHtml = $this->$method($dbname,$dbname,$src,false);
                            $colHtml = str_replace($qettp.$dbname,$this->getExtendTablePrefix().$extendTableId."[".$dbname."][]", $colHtml);
                            $jsArray  = commonTools::getDataArray($this->customJs,"initCol_".$dbname,true,false);
                           // print_r($jsArray);
                            foreach($jsArray as $key => $js)
                            {
                                 $addjs.=$js;
                            }
                            if(isset($this->customJs["initCol_".$dbname])&&$this->customJs["initCol_".$dbname]!=null&&trim($this->customJs["initCol_".$dbname])!="")
                             {
                               
                                $addjs.=$this->customJs["initCol_".$dbname];
                            }
                            $this->setSearchPrefix($searchPrefix);                      
                        }
                    }

                    $qet->setCol($dbname,$title,$colHtml,$addjs);
                }

                
                $addText = "Add";
                $deleteText = "Delete";
                if($this->extendSetting[$extendTableId]["addText"]!=null&&trim($this->extendSetting[$extendTableId]["addText"])!="")
                {
                     $addText = $this->extendSetting[$extendTableId]["addText"];
                }
                if($this->extendSetting[$extendTableId]["deleteText"]!=null&&trim($this->extendSetting[$extendTableId]["deleteText"])!="")
                {
                     $deleteText = $this->extendSetting[$extendTableId]["deleteText"];
                }
                $fixDataList = $this->getFixDataForExtendTable($extendTableId,$dataArray,$deleteText);
                $hiddenColArray = $this->getExtendTableHiddenCol($extendTableId);
                $html = $qet->getHtml($this->getExtendTablePrefix().$extendTableId,$mustHaveColNum,$initColNum,$fixDataList,$addText,$deleteText,$hiddenColArray);
             }
             if($js!=null&&trim($js)!="")
             {
                $html.= "<script>".$js."</script>";
             };
             return $html;
         }

        public function loadValidateFromDb($extendTable=false,$extendTableId=null)
        {
            $editField = null;
            $extendTableDetail = null;
            $sign = true;
            if($extendTable)
            {
                $editField =$this->extendTableCol[$extendTableId];
                $etdInfo = $this->extendTableData[$extendTableId];
                if(is_array($etdInfo))
                {
                    $db = $this->getDb();
                    $tableName = $etdInfo["tablename"];
                    $key = $etdInfo["key"];
                    $etd = new Data($db,$tableName,$key);
                    $extendTableDetail = $etd->getColDetail();
                }
                else
                {
                    $sign = false;
                }
                   
            }
            else
            {
                $editField =  $this->getEditField();
            }
            if($sign)
            {    
            foreach($editField as $dbname => $info)
            {
                $colDetail = null;
                if($extendTable)
                {
                   $colDetail = $extendTableDetail[$dbname];
                }
                else
                {
                    $colDetail = $this->colDetail[$dbname];
                }
             
                if(is_array($colDetail))
                {
                    if((strtolower(trim($colDetail["null"]))=="no"||(isset($colDetail["nullable"])&&intval(trim($colDetail["nullable"]))==0))&&!isset($this->validateRulesMapping["rules"][$this->getEditPrefix().$dbname]["required"]))
                    {
                      if($extendTable)
                      {
                        $this->addExtendValidateRule($extendTableId,$dbname,"required",true);
                        //$this->addExtendValidateRule($extendTableId,$dbname,"min",1);
                      }
                      else
                      { 
                         $this->validateRulesMapping["rules"][$this->getEditPrefix().$dbname]["required"] = true;
                         //$this->validateRulesMapping["rules"][$this->getEditPrefix().$dbname]["min"] = 1;
                      }
                    }
                    if(((isset($colDetail["length"])&&intval(trim($colDetail["length"]))>0)||(isset($colDetail["LENGTH"])&&intval(trim($colDetail["LENGTH"]))>0))&&!isset($this->validateRulesMapping["rules"][$this->getEditPrefix().$dbname]["maxlength"]))
                    {
                      if($extendTable)
                      {
                        $this->addExtendValidateRule($extendTableId,$dbname,"maxlength",intval(trim($colDetail["length"])));
                      }
                      else
                      {
                          $this->validateRulesMapping["rules"][$this->getEditPrefix().$dbname]["maxlength"] = intval(trim($colDetail["length"]));
                      }
                    }
                    $type = strtolower(trim($colDetail["type"]));
                    if($type==null||trim($type)=="")
                    {
                         $type = strtolower(trim($colDetail["type_name"]));
                    }
                    if(StringTools::isStartWith($type,"bigint")
                        ||StringTools::isStartWith($type,"int")
                        ||StringTools::isStartWith($type,"mediumint")
                        ||StringTools::isStartWith($type,"smallint")
                        ||StringTools::isStartWith($type,"tinyint")
                        )
                    {   
                        if($extendTable)
                        {
                                $this->addExtendValidateRule($extendTableId,$dbname,"digits",true);
                        }
                        else if(!isset($this->validateRulesMapping["rules"][$this->getEditPrefix().$dbname]["digits"]))
                        {

                            $this->validateRulesMapping["rules"][$this->getEditPrefix().$dbname]["digits"] = true;
                        }
                    }
                    else if(StringTools::isStartWith($type,"decimal")
                            ||StringTools::isStartWith($type,"double")
                            ||StringTools::isStartWith($type,"float")
                            ||StringTools::isStartWith($type,"numeric")
                            ||StringTools::isStartWith($type,"money")
                            ||StringTools::isStartWith($type,"smallmoney"))
                    {
                        if($extendTable)
                        {
                                $this->addExtendValidateRule($extendTableId,$dbname,"number",true);
                        }
                        else if(!isset($this->validateRulesMapping["rules"][$this->getEditPrefix().$dbname]["number"]))
                        {
                             $this->validateRulesMapping["rules"][$this->getEditPrefix().$dbname]["number"] = true;
                        }
                    }


                    }
                }
            }
        }
        public function getColDetail()
        {
            $detailInfo = Array();
            foreach($this->tables as $key=>$tableinfo)
            {
                $tablename = $tableinfo["tablename"];
                $pk = $tableinfo["pk"];
                $data = new Data($this->db,$tablename,$pk);
                $detailInfo[$key] = $data->getColDetail();
            }
            foreach($this->fields as $dbname => $info)
            {
                $oriDbName = $info["oridbname"];
                $tablekey = DbTools::getTableKeyFormCol($oriDbName);
                $colname = strtolower(DbTools::getColNameFormCol($oriDbName));
                 $this->colDetail[$dbname] = null;
                 if(isset($detailInfo[$tablekey][$colname]))
                {
                    $this->colDetail[$dbname] = $detailInfo[$tablekey][$colname];
                }
            }
        }



        public function setOrderMethod($orderMethod)
        { 
            $this->orderMethod = $orderMethod;
        }
        public function getOrderMethod()
        {
          return $this->orderMethod;
        }
        public function orderMethod($src,$orderField)
        {
            return $orderField;
        }
        public function setSearchMethod($searchMethod)
        {
            $this->searchMethod = $searchMethod;
        }
        public function getSearchMethod()
        {
            return $this->searchMethod;
        }
        public function dataTablesSearchMethod($src,$whereClause)
        {

            $result = $whereClause;
            if($src['search']['value']!=null&&trim($src['search']['value'])!="")
            {
                $colInfo = $this->getColInfo();
                foreach($this->getReportField() as $dbname => $fields)
                {
                   $dstdbname = $this->getSearchFieldMapping($dbname);
                   if($dstdbname == null || trim($dstdbname) == "")
                   {
                        $dstdbname  = $dbname;
                   }
                   if($colInfo[$dstdbname]!=null&&trim($colInfo[$dstdbname])!="")
                   {
                       $fieldInfo = $this->fields[$dstdbname];
                       $islinkfield = $fieldInfo["islinkfield"];
                       $colname = null;
                       if(!$islinkfield)
                       {
                           $colname = $this->getOriDbName($dstdbname);
                           if($this->searchField[$dstdbname]["oridbname"]!=null&&trim($this->searchField[$dstdbname]["oridbname"])!="")
                           {
                                $colname = $this->searchField[$dstdbname]["oridbname"];
                           }
                       }
                       else
                       {
                            $colname = $fieldInfo["linkdatasign"].".".$fieldInfo["linkkey"];
                       }
                       $result.="OR ".$colname." LIKE '%".$src['search']['value']."%' ";
                   }
                }
                $result = ltrim($result,"OR");
                if(trim($result)!="")
                {
                    $result = " (". $result.") ";
                    if($whereClause!=null&&trim($whereClause)!=null)
                    {
                        $result = " AND ".$result;
                    } 
                    $result = $whereClause.$result;
                }
            }
            return $result;
        }
        public function ingridOrderMethod($src,$orderField)
        {
            $sort = $src["sort"];
            $dir = strtoupper($src["dir"]);
            return $this->jsOrderMethod($sort,$dir,$orderField);
        }
        public function dataTablesOrderMethod($src,$orderField)
        {
    
            $sort = $src['order'][0]['column'];
            $dir = strtoupper($src['order'][0]['dir']);
            $draw = intval($src['draw']);
            if($draw > 1)
            {
              $orderField = $this->jsOrderMethod($sort,$dir,$orderField);     
            }
            return $orderField;
        }
        public function getStatisticsHtml($setname,$src=null)
        {
        
            $html = null;
            $array = $this->statisticsGroup[$setname];
            if(is_array($array))
            {

                $methodname = $array["methodname"];
                $cols =  $array["cols"];
                $spiltBy = $array["spiltBy"];
                $multiStatistic = $array["multiStatistic"];
                $result = $this->getResult();
                $result = $this->processStatisticData($result,$setname,$src);
                $statArray = null;
                if($multiStatistic)
                {
                    $statArray = $this->getStatisticsBySet($setname,$result,$cols,$spiltBy);
                }
                else
                {
                    $statArray = $this->getStatistics($setname,$result,$methodname,$cols,$spiltBy);
                }

                $statArray = $this->getTranslateResult($setname,$statArray);
                 // print_r($this->getStatisticDataList($setname,$statArray));
                $html = $this->getStatisticsTable($setname,$statArray);
            }
            return $html;  
        }
        protected function jsOrderMethod($sort,$dir,$orderField)
        {
            
            
           if($sort!=null&&$sort!="")
            {

                $sort = intval($sort);
                if(!($sort==0&&is_array($this->getMainIdCol())&&($this->isEdit()||$this->isDelete()||$this->isChoose())&&!$this->isExportMode()))
                {
                   $dbname = trim($this->getDbNameByOrder($sort));
                    $oldOrder = $this->getOrderField();
                    $orderField = Array();
                    $orderField[$dbname] = $dir;
                    foreach($oldOrder as $d => $o)
                    {
                      if(strtoupper(trim($d))!=strtoupper(trim($dbname)))
                      {
                           $orderField[$d] = $o;
                      }
                    }

                } 

            }
            return $orderField;
        }
        public function isLayoutAdd()
        {
            return $this->isLayoutAdd;
        }

        public function isLayoutDelete()
        {
            return $this->isLayoutDelete;
        }

        public function isLayoutModify()
        {
            return $this->isLayoutModify;
        }

        public function isLayoutCopy()
        {
             return $this->isLayoutCopy;
        }

        public function setIsLayoutAdd($isLayoutAdd)
        {
            $this->isLayoutAdd = $isLayoutAdd;
        } 

        public function setIsLayoutDelete($isLayoutDelete)
        {
            $this->isLayoutDelete = $isLayoutDelete;
        } 

         public function setIsLayoutModify($isLayoutModify)
        {
            $this->isLayoutModify = $isLayoutModify;
        } 

        public function setIsLayoutCopy($isLayoutCopy)
        {
            $this->isLayoutCopy = $isLayoutCopy;
        } 




        public function setLayoutFieldType($row,$dbname,$method="equalTextSearchShowMode",$width=null,$save=true)
        {
            $row = intval($row);
            $array = $this->layoutData[$row];
            if(!is_array($array))
            {
                $array = Array();
            }
            $temp = Array(
      
                            "dbname"=>$dbname,
                            "method"=>$method,
                            "width" =>$width,
                          );
            $array[] = $temp;
            $this->layoutData[$row] = $array;
            $this->layoutField[$dbname] =  $temp;
            $this->keyMapping[$dbname] = $save;    
         }

        public function showLayoutFieldType($array,$src=null)
        {
            $method = $array["method"];
            $dbname = $array["dbname"];
            $searchPrefix = $this->getSearchPrefix();
            $this->setSearchPrefix($this->getEditPrefix());
            $colname = $this->getOriDbName($dbname);
            $value = $src[$dbname];
            $result = $this->$method($dbname,$colname,null,false,$value);
            $result = $this->addAttrJs($dbname, $result);
            $this->setSearchPrefix($searchPrefix);
            return $result;
        }

        public function getLayoutData()
        {
            $layoutData = $this->layoutData;
            ksort($layoutData);
            return $layoutData;
        }
        

        public function setBlank($blank)
        {
           $this->blank = $blank;
        }

        public function isBlank()
        {
           return $this->blank;
        }
        public function setIsDetail($detail)
        {
           $this->isDetail = $detail;
        }

        public function isDetail()
        {
            return $this->isDetail;
        } 

        public function setDetailName($detailName)
        {
            $this->detailName = $detailName;
        }        

        public function getDetailName()
        {
            return $this->detailName;
        }

        public function setIsChoose($choose)
        {
          $this->isChoose = $choose;
        }

        public function isChoose()
        {
           return $this->isChoose;
        }
       
        public function addTransfer($id,$value)
        {
            $this->transfer[$id] = $value;
        }

        public function getTransfer()
        {
            return $this->transfer;
        }

        public function processData($db,$src,$dataArray,$edit=false)
        {
            return $dataArray;
        }
        public function getSearchBar()
        {
            return  ($this->searchBar&&count($this->getSearchField())>0);
        }

         public function getExport()
         {
             return ($this->export&&count($this->getExportField())>0);
         }

          public function getPageExport()
         {
             return ($this->pageexport&&count($this->getExportField())>0);
         }

        public function addColRelation($dst,$src)
        {
            if($this->colRelation==null)
            {
                $this->colRelation = Array();
            }
            $this->colRelation[$dst] = $src;
        }
        
        public function setWhereClause($whereClause)
        {
            $this->whereClause = $whereClause;
        }

        public function getWhereClause()
        {
            return $this->whereClause;
        }

        public function setFormName($formName)
        {
           $this->setReportName($formName);
        }
        
        public function getFormName()
        {
            return $this->getReportName();
        }

        public function getCheckBoxId()
        {
            return $this->checkboxid;
        }
        public function setCheckBoxId($checkboxid)
        {
            $this->checkboxid = $checkboxid; 
        }
        public function isDelete()
        {
            return $this->isDelete;
        }

        public function setIsDelete($isDelete)
        {
             $this->isDelete = $isDelete;
        }

        public function isAdd()
        {
            return $this->isAdd;
        }

        public function setIsAdd($isAdd)
        {

            $this->isAdd = $isAdd;
        }
        public function getEditHiddenStr($dataArray,$src)
        {
            $hidden = $this->getHidden();
            $tables = $this->getTables();
            $filed = $this->getEditField();
            $searchMapping = $this->getSearchMapping();
            foreach($searchMapping as $dbname =>$key)
            {
                $value = $src[$key];
                $this->addHidden($key,$value);
            }
            foreach($tables as $key => $tableInfo)
            {
                if(!(isset($filed[$tableInfo["pk"]])&&$filed[$tableInfo["pk"]]["save"]))
                {
                  $id = $this->getEditPrefix().$tableInfo["pk"];
                  $html = new HtmlElement($id,$id);
                  $value = ArrayTools::getValueFromArray($dataArray,$tableInfo["pk"]);
                  $hidden[$id] = $html->getHidden($value);
                }
            }
            $searchMapping = $this->getSearchMapping();
            return $this->getHiddenStr($hidden);
        }
        public function isEdit()
        {
             return $this->isEdit;
        }

        public function setIsEdit($isEdit)
        {
            $this->isEdit = $isEdit;
        }

        protected function loadPackageCol($dataArray)
        {
            foreach($packageCol as $packageColId =>$colInfo)
            {
                $dataStr = $dataArray[$packageColId];
                if($dataStr!=null&&$dataStr!="")
                {
                    $data = json_decode($dataStr);
                    foreach($data as $key =>$value)
                    {
                        $dataArray[$key] = $value;
                    }
                }
            }
            return $dataArray;
        }
       
        public function getFormDataByMainId($db,$mainId=null,$getArray=true)
        {
             $result = Array();
             if($mainId!=null&&trim($mainId)!="")
             {
                $datamsg = new DataMsg();
                $sql = $this->getSqlByMainId("'".$mainId."'");   
                   if($this->getDebug())
                   {
                     echo $sql."<br>";
                   }
                    
                $result = $datamsg->getUniData($db,$sql,$getArray);
                $result = $this->loadPackageCol($result);
                if(count($result)>0)
                {
                    $tmp = Array($result);
                    $this->getAllLinkData($tmp);
                }
             }
             return $result;
        }
   
       public function initEditCustomField($db,$mainId=null,$getArray=true,$result=null)
       {

       } 


        public function deleteLinkData($dataArray)
        {
            $sqlArray = Array();
            $db = $this->getDb();
            
            if(count($this->deleteLinkTables)>0)
            {
                $this->getAllLinkData($dataArray);
                foreach($this->deleteLinkTables as $dataSign)
                {
                    if(isset($this->linkInfo[$dataSign]))
                    {
                       
                        $srcdbname = $this->linkPath[$dataSign]["srcdbname"];
                        $srcdbInfo = $this->fields[$srcdbname];
                        
                        $ismultivalueinrow =  $srcdbInfo["ismultivalueinrow"];
                        $spiltBy = $srcdbInfo["spiltby"];
                        $pkData = $this->linkData[$dataSign]["id"];
                        $idstrs = "";
                        $tmp = Array();
                        foreach($dataArray as $rowData)
                        {
                            $value = strval($rowData[$srcdbname]);
                            
                            if(!$ismultivalueinrow)
                            {
                               $tmp = array_merge($tmp,$pkData[$value]);
                            }
                            else
                            {
                              
                                $valArr = explode($tmp, $spiltBy);
                                foreach($valArr as $val)
                                {
                                    $tmp = array_merge($tmp,$pkData[$val]);
                                }
                            }
                        }
                        foreach($tmp as $t)
                        {
                            $idstrs.=",'".$t."'";
                        }
                        $idstrs = ltrim($idstrs,",");
                        if($idstrs!=null&&trim($idstrs)!=null)
                        {
                            $pk = $this->linkInfo[$dataSign]["pk"];
                            $tablename = $this->linkInfo[$dataSign]["tablename"];
                            $data = new Data($db,$tablename,$pk);
                            $data->setWithOperator($pk,"(".$idstrs.")","IN"); 
                            $sqlArray[] = $data->delete(true);
                        }
                    }
                }
            }
            return $sqlArray;

        }

        
        public function deleteFormDataByMainId($db,$src=null)
        { 
          
            $temp = $this->modifyDataBeforeDelete($db,$src,$this->editPrefix);
            if(is_array($temp))
            {
               $src = $temp;
            }
            $mainIds = null;
            if($src!=null&&is_array($src))
            {
                 $mainIds =   ArrayTools::getValueFromArray($src,'deleteid');
            }
            
            $beforeResult = $this->beforeDelete($db,$mainIds,$src);
            if(is_bool($beforeResult)&&!$beforeResult)
            {
                return $beforeResult;
            }
  
            if($mainIds!=null&&trim($mainIds)!="")
            {

                $mainIds ='('.$mainIds.')';
                $sql = $this->getSqlByMainId($mainIds,"IN");
                $sql.=$this->getGroupSql().$this->getHavingSql();
                $datamsg = new DataMsg(); 
                $datamsg->findBySql($db,$sql);
                $sqlArray = array();
                for($i=0;$i<$datamsg->getSize();$i++)
                {
                      $data = $datamsg->getData($i);
                      $data->setTables($this->getDeleteTables());
                      $dataArray = $data->getDataArray();
                      $deleteMsg = $data->getMtDataMsg();
                      $sqlArray = array_merge($sqlArray,$deleteMsg->batchDeleteByPrimaryKey(true));
                      $sqlArray = array_merge($sqlArray,$this->deleteExtendTable($dataArray));
                      $sqlArray = array_merge($sqlArray,$this->deleteListTable($dataArray));
                     
                }
                $sqlArray = array_merge($sqlArray,$this->deleteLinkData($datamsg->getResultArray()));
                if(count($sqlArray)>0)
                {
                   if($this->getDebug())
                   {
                      print_r($sqlArray);
                   }
                   $result = $datamsg->batchExec($db,$sqlArray);
                   $this->afterDelete($db,$mainIds,$src); 
                    $afterResult = null;
                    if(is_bool($result)&&$result)
                    {  
                        $afterResult = $this->afterDelete($db,$mainIds,$src);
                    }
                    if(is_bool($afterResult)&&!$afterResult)
                    {
                      return $afterResult;
                    }
                   return $result;
                }
                return false;
            }
        }

        public function beforeDelete($db,$mainIds=null,$src=null)
        {

        }


        public function afterDelete($db,$mainIds=null,$src=null)
        {

        }

        public function updateFormData($db,$src,$editPrefix=null)
        {

            if($editPrefix==null)
            {
                $editPrefix = $this->getEditPrefix(); 
            }
            $src =  $this->prepareCheckboxsDataForEdit($src);
            $temp = $this->modifyDataBeforeUpdate($db,$src,$editPrefix);
            if(is_array($temp))
            {
               $src = $temp;
            }
            $beforeResult = $this->beforeUpdate($db,$src,$editPrefix);
            if(is_bool($beforeResult)&&!$beforeResult)
            {
              return false;
            }
            $result = false;
            $tmp = $this->saveFormData($db,$src,false,$editPrefix);
            $afterResult = null;
              if(is_array($tmp)&&is_bool($tmp["result"]))
             {
                 $result = $tmp["result"];
                 if($result)
                 {
                    $afterResult = $this->afterUpdate($db,$tmp["src"],$editPrefix);
                 }
             }
               if(is_bool($afterResult)&&!$afterResult)
               {
                return $afterResult;
               }
            return $result;
        }

        public function afterUpdate($db,$src,$editPrefix=null)
        {

        }

         public function beforeUpdate($db,$src,$editPrefix=null)
        {

        }

        public function beforeInsert($db,$src,$editPrefix=null)
        {

        }

        public function modifyDataBeforeInsert($db,$src,$editPrefix=null)
        {

        }

        public function modifyDataBeforeUpdate($db,$src,$editPrefix=null)
        {

        }

        public function modifyDataBeforeDelete($db,$src,$editPrefix=null)
        {

        }

       
        public function insertLinkData($src)
        {
            
            
        }
       
        public function insertFormData($db,$src,$editPrefix=null)
        {   
            if($editPrefix==null)
            {
                $editPrefix = $this->getEditPrefix(); 
            }
            $src =  $this->prepareCheckboxsDataForEdit($src);

            $temp = $this->modifyDataBeforeInsert($db,$src,$editPrefix);
            if(is_array($temp))
            {
               $src = $temp;
            }

            $beforeResult = $this->beforeInsert($db,$src,$editPrefix);
           
            if(is_bool($beforeResult)&&!$beforeResult)
            {
              return false;
            }
            $result =false;
            $tmp = $this->saveFormData($db,$src,true,$editPrefix);
            $afterResult = null;

             if(is_array($tmp)&&is_bool($tmp["result"]))
             {
                 $result = $tmp["result"];
                 if($result)
                 {
                    $afterResult = $this->afterInsert($db,$tmp["src"],$editPrefix);
                 }
             }
           
               if(is_bool($afterResult)&&!$afterResult)
               {
                return $afterResult;
              }
            return $result;
        }

      

        public function afterInsert($db,$src=null,$editPrefix=null)
        {

        }
       
        public function prepareCheckboxsDataForEdit($src,$prefix=null)
        {
            $checkboxsForEdit =$this->getCheckboxsForEdit();
            if($prefix==null)
            {
                $prefix = $this->getEditPrefix();
            }
            foreach($checkboxsForEdit as $dbname=>$sign)
            {
                $sign = $this->getEditPrefix().$sign;
                $temp = $src [$sign];
                $result = "";
                if (is_array ( $temp ) && count ( $temp )) {
                  foreach ( $temp as $t ) {
                    if ($t != null && trim ( $t ) != "") {
                      if (trim ( $result ) != "") {
                        $result .= ",";
                      }
                      $result .= $t;
                    }
                  }
                }
                $src[$sign] = $result;
            }
            
            return $src;
        }

        public function quickUpdate($src)
        {
            $colSign = $src["colsign"];
            $dataArray = CommonTools::getDataArray($src,$colSign);
            /*if($this->colRelation!=null&&is_array($this->colRelation))
            {
              foreach ($this->colRelation as $dstdbname => $srcdbname)
              {
                 if(isset($dataArray[$srcdbname]))
                 {
                    $dataArray[$dstdbname] = $dataArray[$srcdbname];
                 }
              }
            }*/
            $id = ArrayTools::getValueFromArray($src,"mainid");
            $value =ArrayTools::getValueFromArray($src,"value");
            $dbname = ArrayTools::getValueFromArray($src,"dbname");
            $tables = $this->getTables();

            $data = $this->getFormDataByMainId($this->getDb(), $id,false);
            foreach($dataArray as $key=>$value)
            {
                $data->set($key,$value);
            }
            $data->setSql($this->getSql($src));
            $data->setTables($tables);
            $cols = $data->getCols();

            $col = $cols[$dbname];
            $tmp = explode(".", trim($col));
          
            $tablekey = $data->getTableKeyFormCol(trim($col));
            $dataMsg = $data->getMtDataMsg($tablekey);
             
            if($this->getDebug())
            {        
               print_r($dataMsg->batchUpdate(true));  
            }
            if($dataMsg->batchUpdate())
            {
                $method = $this->getAfterQuickUpdateMethod($dbname);
                if($method!=null&&trim($method)!="")
                {
                    $this->$method($this->getDb(),$dbname,$data->getDataArray());   
                }
            }

        }

        public function deleteExtendTable($dataArray)
        {
            $db = $this->getDb();
            $result = Array();

            foreach($this->extendTable as $id =>$tableInfo)
            {

                if(isset($this->extendTableData[$id]))
                {
                    $dataInfo = $this->extendTableData[$id];
                     if(isset($this->extendSetting[$id]["deleteMethod"])&&$this->extendSetting[$id]["deleteMethod"]!=null&&trim($this->extendSetting[$id]["deleteMethod"])!="")
                    {   
                        $method = $this->extendSetting[$id]["deleteMethod"];
                        $tmp = $this->$method($id,$dataArray,$dataInfo);
                        if(is_array($tmp))
                        {
                            $result = $tmp;
                        }
                    }
                    else
                    {
                        $tablename = $dataInfo["tablename"];
                        $pkey =  $dataInfo["key"];
                        $linkkey = $dataInfo["linkkey"];
                        $dstdbname = $dataInfo["dstdbname"];
                        $linkkeyvalue = $dataArray[$dstdbname];
                        if($linkkeyvalue!=null&&trim($linkkeyvalue)!="")
                        {
                            $data = new Data($db,$tablename,$linkkey);
                            $data->set($linkkey,$linkkeyvalue);
                            $result[] = $data->delete(true);
                        }
                    }
                }
            }
            return $result;
        }

        public function deleteListTable($dataArray)
        {
            $db = $this->getDb();
            $result = Array();
            foreach($this->listTable as $tableid => $tableInfo)
            {
                $dbname = $tableInfo["dbname"];
                $listTableName = $tableInfo["listTableName"];
                $listTableId = $tableInfo["listTableId"];
                $listKey = $tableInfo["listKey"];
                $value = $dataArray[$dbname];
                if($value!=null&&trim($value)!="")
                {
                      $data = new Data($db,$listTableName,$listTableId);
                      $data->set($listKey,$value);
                      $result[] = $data->delete(true);
                }

            }
            return $result;
        }

        public function saveListTable($src,$data,$listTablePrefix=null,$editPrefix=null)
        {

            $db = $this->getDb();
            $sqlArray = Array();
            if($listTablePrefix==null||trim($listTablePrefix)!="")
            {
                $listTablePrefix = $this->getListTablePrefix();
            }
            if($editPrefix==null||trim($editPrefix)!="")
            {
                $editPrefix = $this->getEditPrefix();
            }
            foreach($this->listTable as $tableid => $tableInfo)
            {
                $listKey = $listTablePrefix.$tableid."_".$tableInfo["listKey"];
                $listKeyValue = $src[$listKey];
                if($listKeyValue==null||trim($listKeyValue)=="")
                {     
                    $dbname = $tableInfo["dbname"];
                    $listKeyValue = $data->get($dbname);
                   
                }
                $dstdbname = $listTablePrefix.$tableid."_".$tableInfo["data"]["dstdbname"];
                $dstdbnameArray = $src[$dstdbname];
                if(is_array($dstdbnameArray))
                {
                    $listTableName = $tableInfo["listTableName"];
                    $listTableId = $tableInfo["listTableId"];
                    for($i=0;$i<count($dstdbnameArray);$i++)
                    {
                        $data = new Data($db,$listTableName,$listTableId);
                        $data->set($tableInfo["data"]["dstdbname"],$dstdbnameArray[$i]);
                        $data->set($tableInfo["listKey"],$listKeyValue);
                        $listTableIdKey = $listTablePrefix.$tableid."_".$listTableId;
                        $data->set($listTableId,$src[$listTableIdKey][$i]);
                        foreach($tableInfo["cols"] as $col => $colInfo)
                        {
                            $objid =  $listTablePrefix.$tableid."_".$col;
                            $data->set($col,$src[$objid][$i]);
                        }
                        $sqlArray[] = $data->createUpdate(true); 
                    }
                };
            }

            return $sqlArray;

        }

        public function saveExtendTable($src,$extendTablePrefix=null,$editPrefix=null)
        {
            $db = $this->getDb();

            if($extendTablePrefix==null||trim($extendTablePrefix)!="")
            {
                $extendTablePrefix = $this->getExtendTablePrefix();
            }
            if($editPrefix==null||trim($editPrefix)!="")
            {
                $editPrefix = $this->getEditPrefix();
            }

            $result = Array();
            foreach($this->extendTable as $id =>$tableInfo)
            {

                if(isset($this->extendTableData[$id]))
                {

                    $dataInfo = $this->extendTableData[$id];
                    if(isset($this->extendSetting[$id]["saveMethod"])&&$this->extendSetting[$id]["saveMethod"]!=null&&trim($this->extendSetting[$id]["saveMethod"])!="")
                    {   
                        $method = $this->extendSetting[$id]["saveMethod"];
                        $tmp = $this->$method($id,$src,$dataInfo);
                        if(is_array($tmp))
                        {
                            $result = $tmp;
                        }
                    }
                    else
                    {

                        $dataList = $src[$extendTablePrefix.$id];
                        $count = null;
                        foreach($dataList as $key => $valueList)
                        {
                            $count = count($valueList);
                            break;
                        }
                        $tablename = $dataInfo["tablename"];
                        $pkey =  $dataInfo["key"];
                        $linkkey = $dataInfo["linkkey"];
                        $dstdbname = $dataInfo["dstdbname"];
                        $linkkeyvalue = $src[$editPrefix.$dstdbname];
                        for($i=0;$i<$count;$i++)
                        {
                            $data = new Data($db,$tablename,$pkey);
                            $needSave = false;
                            foreach($dataList as $key => $valueList)
                            {
                               
                                if($key!=$linkkey&&$valueList[$i]!=null&&trim($valueList[$i])!="")
                                {
                                    $data->set($key,$valueList[$i]);
                                    $needSave = true;
                                }
                            }
                            if($needSave)
                            {
                                if($linkkeyvalue!=null&&trim($linkkeyvalue)!="")
                                {
                                    $data->set($linkkey,$linkkeyvalue);
                                }

                                $result[] = $data->createUpdate(true);
                            }
                             
                        }
                    }   
                }   
            }
            return $result;
        }

        public function processUpload($dataArray)
        {
            if(!is_array($dataArray))
            {
                $dataArray = Array();
            }
           
            //echo "<br>";
            //print_r($dataArray);
            $ret = Array();
            foreach($_FILES as $dbname => $uploadinfo)
            {
                
                if(StringTools::isStartWith($dbname,$this->getUploadPrefix())&&$uploadinfo["name"]!=null&&trim($uploadinfo["name"])!="")
                {
                  
                    $uploaderInfo = $this->getUploader($dbname);
                 
                    $process = $uploaderInfo["process"];
                    $save = $uploaderInfo["save"];
                    $extension = $uploaderInfo["extension"];
                    $override = $uploaderInfo["override"];
                    $spiltBy = $uploaderInfo["spiltBy"];
                    $path =  $uploaderInfo["path"];
                    $savepath =  $uploaderInfo["savepath"];
                    $uploaderClass = $uploaderInfo["uploader"];
                    $uploader = new $uploaderClass();
                    //echo '@@@'.$dbname;
                    if(isset($this->uploadMaxSize[$dbname]))
                    {
                        $maxsize = strval($this->uploadMaxSize[$dbname]);
                        //echo $maxsize;
                        //die();
                        if($maxsize!=null&&trim($maxsize)!="")
                        {
                            $maxsize = intval($maxsize);
                            $uploader->setMaxSize($maxsize);
                        }
                    }
                    $uploader->setExtension($extension,$spiltBy,$override);
                    $isProcess = false;
                    if($process!=null)
                    {
                        if(is_string($process))
                        {
                            $isProcess = true;
                            $uploader->setProcessMethod($process);
                        }
                    }
                    //print_r($dataArray);
                    //echo $dbname."<br>";
                    //echo $path;
                    //echo "<br>";
                    $result = $uploader->upload($db,$dataArray,$dbname,$path,$isProcess,$dbname);

                    $tmp = substr($dbname,strlen($this->getUploadPrefix()));
                    if(!$result)
                    {
                       $method = $dataArray["method"];
                       $displayname = $this->fields[$tmp]["displayname"];
                       $this->setMethodWarning($method,$displayname.":".$uploader->getErrMsg());
                       return false;
                    }
                    else if($save)
                    { 

                       $ret[$tmp] = $savepath.$uploader->getReturnString();
                    }
                    
                }

            }
            return $ret;
        }

       
        public function modifySaveDataArray($src,$dataArray,$forceAdd=false,$editPrefix=null)
        {
             return $dataArray;
        }

        protected function processPackageCol($dataArray)
        {
            foreach($this->packageCol as $packageColId => $colInfo)
            {
                $data = Array();
                foreach($colInfo as $col => $removeFromSrc)
                {
                    $data[$col]  = $dataArray[$col];
                    if($removeFromSrc)
                    {
                        unset($dataArray[$col]);
                    }
                    $dataArray[$packageColId] = json_encode($data);   
                }
            }
            return $dataArray;

        }

        protected function saveFormData($db,$src,$forceAdd=false,$editPrefix=null)
        { 
            //print_r($src);

            if($editPrefix==null)
            {
               $editPrefix = $this->getEditPrefix(); 
            }
            $src = $this->processPackageCol($src);
            
            $fileArray = $this->processUpload($src);
            
            if(is_bool($fileArray)&&!$fileArray)
            {
                return false;
            }   
            $dataArray = CommonTools::getDataArray($src, $editPrefix); 
            $dataArray = array_merge( $dataArray,$fileArray);
            $dataArray = $this->modifySaveDataArray($src,$dataArray,$forceAdd,$editPrefix);
            if($this->colRelation!=null&&is_array($this->colRelation))
            {
              foreach ($this->colRelation as $dst => $src)
              {
                 if(isset($dataArray[$src]))
                 {
                    $dataArray[$dst] = $dataArray[$src];
                 }
              }
            }

            $tables = $this->getTables();
             
            $method = trim($src["method"]);

            if($forceAdd)
            {
               foreach($tables as $key => $data)
               {
                 $pk = $data["pk"];
                 $removePk = true;
                 if((isset($this->editField[$pk])&&$method=="add")||(isset($this->layoutaddField[$pk])&&$method=="layoutadd"))
                 {
                    $removePk =false;
                 }
                 if($removePk)
                 {
                    unset($dataArray[$pk]);
                 }
                 else
                 {
                    $tables[$key]["forceadd"] = true;
                 }
               }
            }
            $data = new Data($db);
            $data->setCols($this->getColInfo());
            $data->setDataArray($dataArray);
            $data->setTables($tables);
            $dataMsg = $data->getMtDataMsg();
      
            $execMsg = new DataMsg($db);
            $mainData = null;
            $mainIdCol = $this->getMainIdCol();
            $maindbname = $this->mainIdCol["dbname"];
            $mainOriName =  $this->mainIdCol["oridbname"];
            if($mainOriName==null||trim($mainOriName)=="")
            {
                $colInfo = $this->getColInfo();
                $mainOriName = $colInfo[$maindbname];
            }

            for($i=0;$i<$dataMsg->getSize();$i++)
            {
                $data = $dataMsg->getData($i);
                if($data->getTableKeyFormCol($mainOriName)==$data->getTableSign())
                {
                    $mainData = $data;
                }
                else
                {
                    $execMsg->addData($data);
                }
            }
           
            $fullData = $data;
            $result = true;
            if($mainData!=null)
            {

                $isUpdate = $mainData->hasPrimaryKeyValue();

                $data->setDb($db);
               
                $result = $mainData->createUpdate(false,false,true);

                 if($this->getDebug())
                 {
                            echo $mainData->createUpdate(true,false,true);

                 }
                $this->primaryKeyValueArray[$mainData->getTableSign()] = $mainData->getPrimaryKeyValue();
                $tmpSrc = $src;

                if(!$isUpdate)
                {
                    $tmpSrc[$editPrefix.$this->getMainIdDbName()] = $result;
                    $fullData->set($maindbname,$result);
                }
               
                $tmpArray = $this->saveExtendTable($tmpSrc,null,$editPrefix);
             
                if(is_array($tmpArray)&&count($tmpArray)>0)
                {
                    if($this->getDebug())
                    {
                        print_r($tmpArray);
                    }
                    $result = $db->execTransaction($tmpArray);
                }

                if(!$isUpdate||$forceAdd)
                {   
                    $src[$editPrefix.$maindbname] = $result;
                    $tmpSrc = $src;
                }

                if($result)
                {
                    $tmpArray  = $this->saveListTable($tmpSrc,$fullData,null,$editPrefix);
                    if(is_array($tmpArray)&&count($tmpArray)>0)
                    {
                         if($this->getDebug())
                        {
                            print_r($tmpArray);
                        }
                        $result = $db->execTransaction($tmpArray);
                    }
                }
            }

            if($result&&$execMsg->getSize()>0)
            {

                for($i=0;$i<$execMsg->getSize();$i++)
                {
                    $execData = $execMsg->getData($i);
                    $result = $execData->createUpdate(false,false,true);
                        if($this->getDebug())
                        {
                            echo $execData->createUpdate(true,false,true);

                        }
                    $this->primaryKeyValueArray[$execData->getTableSign()] = $execData->getPrimaryKeyValue();
                    if(!$result)
                    {
                        break;
                    }
                }
            }

            if($result)
            {
                $result = true;
            }
            else
            {
                $result = false;
            }
            $array = array("result"=>$result,"src"=>$src);
           
            return $array;
        }

       

        public function getDeleteTables()
        {
            return $this->deleteTables;
        } 

        public function getImportTables()
        {
            return $this->importTables; 
        }

        public function getTables()
        {
            return $this->tables;
        }

        public function setImportTable($pk,$tablename,$key=null,$data=array())
        {
           if($key==null)
           {
               $key = $tablename;
           }
           $this->importTables[trim($key)] = array("pk"=>trim($pk),"tablename"=>trim($tablename),"data"=>$data);
        }
 

        public function setDeleteTable($pk,$tablename,$key=null,$data=array())
        {
           if($key==null)
           {
               $key = $tablename;
           }
           $this->deleteTables[trim($key)] = array("pk"=>trim($pk),"tablename"=>trim($tablename),"data"=>$data);
        }

        public function setTable($pk,$tablename,$key=null,$data=array())
        {
           if($key==null)
           {
               $key = $tablename;
           }
           $this->tables[trim($key)] = array("pk"=>trim($pk),"tablename"=>trim($tablename),"data"=>$data);
        }
       
        public function getSqlByMainId($mainId,$sign="=")
        {
            $sql = $this->getSql(null);
            $mainIdCol = $this->getMainIdCol();
            $colinfo  = $this->getColInfo();
            $colname = $mainIdCol["oridbname"];
            if($colname==null||trim($colname)=="")
            {
             
               $colname = $colinfo[$mainIdCol["dbname"]];
            }
            $sql .= " WHERE ".$colname." ".$sign." ".$mainId." ";
            return $sql;  
        }
           public function getFormDefaultValue($dbname)
          {
              $field = $this->getFieldByName($dbname);
              $defaultValue = ArrayTools::getValueFromArray($field,"defaultsearch");
              return $defaultValue;
          }
        protected  function getFieldArray($array)
        {
            $result = Array();
            foreach($array as $dbname =>$info)
            {
                if($this->getColStatus($dbname))
                {
                    $result[$dbname] = $info;
                }
            }
            return $result;

        }
        public function getExportField()
        {
            return $this->getFieldArray($this->exportField);
        }
        public function getReportField($all=false)
        {
            $reportField = $this->reportField;
            if(!$all)
            {
                $this->getFieldArray($reportField);
            }
            return $reportField;
        }
        public  function getReportHead()
        {
            return $this->reportHead;
        }
        public function setReportHead($reportHead)
        {
            $this->reportHead = $reportHead;
        }
         public function getTitleInfo($all=false)
         {
            $titleinfo = $this->titleInfo;
            if(!$all)
            {
                $titleinfo = $this->getFieldArray($this->titleInfo);
            }
            return $titleinfo;
         }
        public function getDetailField()
        {
          return $this->detailField;
        }
        public function getSearchField()
        {
            return $this->searchField;
        }
        public function getGroupField()
        {
          return $this->groupField;
        }
        public function setOrderField($orderField)
        {
           $this->orderField = $orderField;
        }
        public function getOrderField()
        {
            return $this->orderField;
        }
        public function getEditField()
        {
          return $this->editField;
        }
          public function getQuickEditField()
        {
          return $this->quickEditField;
        }
        public function setJsOrderType($dbname,$jsOrderType)
        {
            $this->jsOrderType[$dbname] = $jsOrderType;
        }

        public function getJsOrderType($dbname)
        {
            $type = ArrayTools::getValueFromArray($this->jsOrderType,$dbname);
            if($type==null||trim($type)=="")
            {
                $type = "CaseInsensitiveString";
            }
            return $type;
        }
      public function initResource($src=null)
      {

      }
      public function addImportField($dbname,$add=true)
      {
          if($add)
          {
             $this->importField[$dbname] = $dbname;
          }
          else
          {
              unset($this->importField[$dbname]);
          }
      }

      public function setImportField($importField)
      {
          $this->importField = $importField;
      }

      public function getImportField()
      {
         return $this->importField;
      }




        public function addFormField($dbname,$displayName="",$oriDbName=null,$defaultsearch=null,$isChecked=true)
        {
             $this->fieldsOrder[] = $dbname;
             $isFromDB = true;
             $isCanSearch = true; 
             $colinfo  = $this->getColInfo();
             if($oriDbName == null &&trim($oriDbName)!="")
             {
                $oriDbName =  $colinfo[$dbname];
             }
             if($oriDbName==null||trim($oriDbName)=="")
             {
                 $oriDbName = $dbname;
                 $isFromDB = false;
                  $isCanSearch = false; 
             }
             $this->fields[$dbname] = Array(
                 "displayname"=>$displayName,
                 "isfromdb"=>$isFromDB,
                 "iscansearch"=>$isCanSearch,
                 "ischecked"=>$isChecked,
                 "oridbname"=>$oriDbName,
                 "defaultsearch"=>  $defaultsearch,
             );
        }

         public function getBarCode($row,$dbname,$export)
         {
            $dbname =strval($this->getBarCodeMapping($dbname));
            $result = $this->getValueByDbname($row,$dbname,false);
            $barcode = new Picqer\Barcode\BarcodeGeneratorSVG();
            if(!$export)
            {
              $result = "<div>".$barcode->getBarcode($result,"C128")."<BR>".$result."<div>";
            }
            return $result;      
         }

        public function setQuickEditFieldType($dbname,$quickEdit=null,$key=null,$quickEditMethod="quickUpdate")
        { 
             $this->setReportFieldType($dbname,true,"quickEditShowMode");
              if($key==null)
              {
                $mainIdColInfo  =$this->getMainIdCol(); 
                $key = $mainIdColInfo["dbname"];
             }
             $this->quickEditField[$dbname] = Array("methodname" =>$quickEdit,"key"=>$key,"method"=>$quickEditMethod) ;
        }
        
        public function setRaeFieldType($dbname,$extend=false,$report="defaultShowMode",$ajax=false)
        {
            $this->setReportFieldType($dbname,$extend,$report,$ajax);
            $this->setExportFieldType($dbname,$extend,$report);
        }
        public function setLayoutRaeFieldType($dbname,$initMethod,$ajax=false)
        {
            $this->setLayoutReportFieldType($dbname,$initMethod,$ajax);
            $this->setLayoutExportFieldType($dbname,$initMethod);
        }
        public function setLayoutReportFieldType($dbname,$initMethod,$ajax=false)
        {
            $this->setLayoutColInfo($dbname,$initMethod,false);

            $this->setReportFieldType($dbname,true,"getLayoutColInfoShowMode",$ajax);
        }
        public function setLayoutExportFieldType($dbname,$initMethod)
        {
            $this->setLayoutColInfo($dbname,$initMethod,true);
            $this->setExportFieldType($dbname,true,"getLayoutColInfoShowMode");
        }
        public function setAjaxReportFieldType($dbname,$extend=false,$report="defaultShowMode")
        {
           $this->setReportFieldType($dbname,$extend,$report,true);

        }

          public function setAjaxRaeFieldType($dbname,$extend=false,$report="defaultShowMode")
        {
           $this->setRaeFieldType($dbname,$extend,$report,true);

        }

        protected function addFieldBySql($src,$array=null)
        {
                $sqlArray = DbTools::getColNames($this->getSql($src));
                foreach($sqlArray as $k => $v)
                {
                    $kArray = Array();
                    if($array!=null&&is_array($array)&&isset($array[$k])&&is_array($array[$k]))
                    {
                        $kArray = $array[$k];
                    }

                    $name = "";
                    if(isset($kArray["name"])&&$kArray["name"]!=null&&$kArray["name"]!="")
                    {
                        $name = $kArray["name"];
                    }
                    else
                    {
                       $tmp = Array();
                        $tmp = explode("_", trim($k));

                        foreach($tmp as $t)
                        {
                            $name .=" ".ucfirst(trim($t));
                        }
                        $name = ltrim($name);
                    }
                  
                    $this->addField($k,$name);
                    $report = "defaultShowMode";
                    if(isset($kArray["report"]))
                    {
                        $report = $kArray["report"];
                    }
                    
                    $ajax = false;
                    if(isset($kArray["ajax"])&&is_bool($kArray["ajax"])&&$kArray["ajax"])
                    {
                        $ajax = true;
                    }
                    $reportExtend = false;
                    if($report!=null&&trim($report)!="")
                    {
                       
                        $reportExtend = true;
                    }
                    $export = "defaultShowMode";
                    if(isset($kArray["export"]))
                    {
                        $export = $kArray["export"];
                    }
                    $searchExtend = false;
                    if(isset($kArray["search"]))
                    {
                        $searchExtend = $kArray["search"];
                    }
                   
                    $exportExtend = false;
                    if($export!=null&&trim($export)!="")
                    {
                        $exportExtend = true;
                    }
                    $this->setReportFieldType($k,$reportExtend,$report,$ajax);
                    $this->setExportFieldType($k,$exportExtend,$export,$ajax);
                    $this->setSearchFieldType($k,$searchExtend);
                }
        }
        public function setReportFieldType($dbname,$extend=false,$report="defaultShowMode",$ajax=false)
        {      
            if($report!=null&&$report!==false)
            {
              if($ajax)
              {
                $report = "getAjaxDataBy".$report;
              }
              $this->reportField[$dbname] = $report;
            }
            else
            {
               unset($this->reportField[$dbname]);
            }
     
        }

        public function setGroupFieldType($dbname,$group=true)
        {
            if($group)
            {
              $this->groupField[$dbname] = $group;
            }
            else
            {
               unset($this->groupField[$dbname]);
            }
        }


        public function setExportFieldType($dbname,$extend=false,$export="defaultShowMode")
        {
            if($export!=null&&$export!==false)
            {
              $this->exportField[$dbname] = $export;
            }
            else
            {
               unset($this->exportField[$dbname]);
            }
        }

        public function filterResult($db,$src)
        {
            $result = $this->getResult();
            foreach($this->filteMap as $dbname =>$filter)
            {
                $value = $src[$this->getSearchPrefix().$dbname];
                if($value != null &&trim($value)!="" )
                {
                    $result = $this->$filter($db,$dbname,$src,$result);
                }
            }
            $this->setResult($result);
        }

        public function defaultFilter($db,$dbname,$src,$result)
        {
            $value = $src[$this->getSearchPrefix().$dbname];
            $newResult = Array();
            foreach($result as $data)
            {
                if(strtoupper(trim($data[$dbname])) == strtoupper(trim($value)))
                {
                    $newResult[] = $data;
                }
            }
            return $newResult;
        }

        public function setFilterFieldType($dbname,$search=null,$filter="defaultFilter",$defaultsearch=null)
        {
            $this->setSearchFieldType($dbname,$search,$defaultsearch,null,false);
            $this->filteMap[$dbname] = $filter;
        }
        public function setHavingSearchFieldType($dbname,$search=null,$defaultsearch=null,$oridbname=null)
        {
            $this->setSearchFieldType($dbname,$search,$defaultsearch,$oridbname,true,true);
        }

        public function setSearchGroupFieldType($groupid,$dbname,$search=null,$defaultsearch=null,$relation="AND",$oridbname=null)
        {
            $this->setSearchFieldType($dbname,$search,$defaultsearch,$oridbname,true,false,$groupid,$relation,"");
        }
        public function getSearchValue($dbname,$src=null,$useDefaultValue=true)
        {
            if($src==null)
            {
                $src=$_REQUEST;
            }
            $value = null;
            if($useDefaultValue)
            {
                $value = $this->fields[$dbname]["defaultsearch"];
            }
            if(isset($src[$this->getSearchPrefix().$dbname]))
            {
                 $value = $src[$this->getSearchPrefix().$dbname];
                
            }
            return $value;

        }
        public function setHavingSearchGroupFieldType($groupid,$dbname,$search=null,$defaultsearch=null,$relation="AND",$oridbname=null)
        {
            $this->setSearchFieldType($dbname,$search,$defaultsearch,$oridbname,true,true,$groupid,$relation,"",false);
        }
        public function setJoinFieldType($dbname,$joinMark,$search=null,$defaultsearch=null,$oridbname=null,$groupid=null,$relation="AND")
        {
            $this->setSearchFieldType($dbname,$search,$defaultsearch,$oridbname,true,false,$groupid,$relation,"",true,$joinMark);
        }
        public function setSearchMethodField($dbname,$search=null)
        {
            $this->searchField[$dbname]["searchmethod"] = $search;
        }
        public function setSearchFieldType($dbname,$search=null,$defaultsearch=null,$oridbname=null,$isSql=true,$having=false,$groupid=null,$relation="AND",$text="",$join=false,$joinMark=null)
        {
            if($groupid==null)
            {
                    $groupid = $dbname;
            }
            if($search!=null&&trim($search)!=""&&$search!==false)
            {
               
                $groupName = $this->getSearchGroupName($groupid);
                if($groupName==null||trim($groupName)=="")
                {
                    $groupName = $this->getDisplayName($dbname);
                    $this->setSearchGroupName($groupid,$groupName);
                }
                $this->setSearchGroup($groupid,$dbname,$text,$relation);
                $this->searchField[$dbname]["having"] = $having;
              $this->searchField[$dbname]["issql"] = $isSql;
              $this->searchField[$dbname]["method"] = $search;
              $this->searchField[$dbname]["join"] = $join;
              $this->searchField[$dbname]["joinMark"] = $joinMark;
              if($oridbname!=null)
              {
                $this->searchField[$dbname]["oridbname"] = $oridbname;
              }
              if($defaultsearch!=null)
              {
                  $this->fields[$dbname]["defaultsearch"] = $defaultsearch;
              }
            }
            else
            {
               unset($this->searchField[$dbname]);
               $this->removeSearchGroup($groupid,$dbname);
            }
        }

        public function setOrderFieldType($dbname,$order="ASC")
        {
            if($order!=null)
            {
              $this->orderField[$dbname] = $order;
            }
            else
            {
               unset($this->orderField[$dbname]);
            }
        }
        public function setUploadMaxSize($dbname,$maxsize)
        {
            $this->uploadMaxSize[$dbname] = $maxsize;
        }
        public function setUploadFieldType($dbname,$path,$extension,$uploader="quickUploader",$process=null,$save=true,$savepath="")
        {
            $this->setEditFieldType($dbname,"uploadEditShowMode",true,true);
            $this->setUploader($this->getUploadPrefix().$dbname,$uploader,$path,$extension,$process,$save,$savepath);

        }

        public function getCheckboxsForEdit()
        {
          return $this->checkboxsForEdit;
        }

        public function setEditMutilFieldType($dbname,$edit=null,$spiltBy=",",$muiltRow=false,$save=true,$upload=false)
        {
            $this->setEditFieldType($dbname,$edit,$save,$upload,true,$splitBy,$muiltRow);
        }
         
        public function setEditFieldType($dbname,$edit=null,$save=true,$upload=false,$isMutil=false,$splitBy=",",$muiltRow=false)
        {
            if($edit!=null)
            {
               $this->editField[$dbname] = array("method"=>$edit,"save"=>$save,"upload"=>$upload,"isMutil"=>$isMutil,"splitBy"=>$splitBy,"muiltRow"=>$muiltRow);
               if(StringTools::isStartWith($edit,"getCheckboxesBy"))
               {
                  $this->checkboxsForEdit[$dbname] = $dbname;

               }
            }
            else
            {
               unset($this->editField[$dbname]);
               unset($this->checkboxsForEdit[$dbname]);
            }
        }

        public function setExportFileFieldType($dbname,$isRealFilePath,$exportFileMethod,$exportExtensionNameCol=true)
        {
             if($exportExtensionNameCol)
             {
                $this->setExportFieldType($dbname,true,$exportFileMethod);
             }
             $this->exportFileField[$dbname] = Array("isRealFilePath"=>$isRealFilePath,"exportFileMethod"=>$exportFileMethod);
        }
        
        public function setDetailFieldType($dbname,$extend=false,$report=null)
        {
            if(!$extend)
            {
              $report = "defaultShowMode";
            }
            if($report!=null)
            {
              $this->detailField[$dbname] = $report;
            }
            else
            {
               unset($this->detailField[$dbname]);
            }
        }

        public function setColDetailFieldType($dbname,$extend=false,$report=null)
        {
            if(!$extend)
            {
              $report = "defaultShowMode";
            }
            if($report!=null)
            {
              $this->colDetailField[$dbname] = $report;
            }
            else
            {
               unset($this->colDetailField[$dbname]);
            }
        }

        
        public function setFormFieldType($dbname,$reportExtend=false,$report="defaultShowMode",$exportExtend=false,$export="defaultShowMode",$search=null,$defaultSeatch=null,$order=null,$edit=null,$save=true)
        {
          $this->setReportFieldType($dbname,$reportExtend,$report);
          $this->setExportFieldType($dbname,$exportExtend,$export);
            $this->setSearchFieldType($dbname,$search,$defaultSeatch);
            $this->setOrderFieldType($dbname,$order);
          $this->setEditFieldType($dbname,$edit,$save);
        } 

        public function getRowNum($row,$dbname,$export=false)
        {
               $curPage = $this->getCurPage();
               $pageRows  = $this->getPageRows();
               $mainId = $this->getMainId($row,$dbname,$export);
               $result = ($curPage-1)*$pageRows+$row+1;
               $result = "<a name='_anchor_".$mainId."'></a>".$result;
               return $result;
        }

        public function getChooseCheckBoxValue($row,$dbname,$export=false)
        {
            $value = null;
            $dbname = $this->getChooseCheckBoxDbName();
            if($dbname!=null&&trim($dbname)!="")
            {
                $value =$this->getValueByDbName($row,$dbname,false);
            }
            else
            {
                $value = $this->getMainId($row,$dbname,$export);
            }
            return $value;
        }


        public function getCheckBoxForMainId($row,$dbname,$export=false)
        {
            $value = $this->getChooseCheckBoxValue($row,$dbname,true);
            $html = new HtmlElement($dbname.'_'.$value,$dbname);
            if($value==null&&trim($value)=="")
            {
                $html->setParam("disabled","disabled");
            }
            return $html->getCheckBox($value);
        }

        public function getViewDetailByMainId($row,$dbname,$export=false)
        {   
            $view = "view";
            if($dbname!="_view_data_detail")
            {
              $view =  $this->getValueByDbName($row,$dbname,false);
            }
            $mainId = $this->getMainId($row,$dbname,$export);
            $html = new HtmlElement();
            $isReport = "0";
            if($this->isReport())
            {
              $isReport = "1";
            }
            $formMark = $this->getFormMark();
            $js = "javascript:_viewDataDetail(\"".$mainId."\",\"".$isReport."\",\"".$formMark."\")";
            return $html->getUrl($view,$js);
        }

        public function getMainId($row,$dbname=NULL,$export=false)
        {   
            $mainIdColInfo  =$this->getMainIdCol();
            $mainIdCol = $mainIdColInfo["dbname"];
            $isMethod = $mainIdColInfo["isMethod"];
            $methodName = $mainIdColInfo["methodName"];
            $result = null;
            if($isMethod&&$methodName!=null&&trim($methodName)!="")
            {
                $result = $this->$methodName($row,$mainIdCol,false);
            }
            else
            {
               $result = $this->getValueByDbName($row,$mainIdCol,false);
            }
            return $result;
        }

        public function getCheckAllCheckBox()
        {
            $id = $this->getCheckBoxId().'_all';
            $html = new HtmlElement($id, $id);
            $js = "_checkAllCheckBox(this)";
            $html->setFunction("onClick",$js);
            return $html->getCheckBox();
        }

        public function getQuickEditShowModeHtml($mainid,$idvalue,$prefix,$dbname,$value,$methodname=null,$export=false,$isAjax=true)
        {
                $html = ArrayTools::getValueFromArray($this->quickEditDefaultText,$dbname);
                if($idvalue!=null&&trim($idvalue)!="")
                {
                   $searchPrefix = $this->getSearchPrefix();
                   $prefix = $prefix.$mainid."_";
                   $this->setSearchPrefix($prefix);
                   $colname = $this->getOriDbName($dbname);
                 
                   if($methodname==null)
                   {
                        $methodname = $this->quickEditField[$dbname]["methodname"];
                   }
                   $method = $this->quickEditField[$dbname]["method"];
                   $idkey =  $this->quickEditField[$dbname]["key"];
                   $html  = $this->$methodname($dbname,$colname,null,false,$value);
                   $colSign = $prefix;
                   $newid = $colSign.$dbname;
                   $this->setAttr($dbname,"id",$newid,false,false,true);
                   $this->setAttr($dbname,"name",$colSign,false,false,true);
                   $this->setAttr($dbname,"key",$idkey,false,false,true);
                   $this->setAttr($dbname,"keyvalue",$idvalue,false,false,true);
                   $this->setAttr($dbname,"mainid",$mainid,false,false,true);
                   $this->setAttr($dbname,"method",$method,false,false,true);
                   $this->setAttr($dbname,"dbname",$dbname,false,false,true);
                  
                  
                   $divprefix = "div_".$prefix;
                    if(!isset($this->voidBootStrap[$dbname])||!$this->voidBootStrap[$dbname])
                    {
                         $this->setAttr($dbname,"class","form-control",false,false,true);
                    }
                  
                   
                   $this->applyColMapping($dbname,"quickEdit",$divprefix,$idvalue,$mainid,$newid);
                   $html = $this->addAttrJs($dbname,$html,"quickEdit");
                   if(!$isAjax)
                   {
                       $divid = $divprefix.$this->getEditPrefix().$dbname;
                       $div = new HtmlElement($divid,$divid);
                       $html = $div->getDiv($html);
                   }
                   $this->setSearchPrefix($searchPrefix);
               }
               return $html;
        }   

        public function quickEditShowMode($row,$dbname,$export=false,$methodname=null)
        {
               $value =  $this->getValueByDbName($row,$dbname,false);  
               $prefix = $this->getQuickEditPrefix();    
               $mainid = $this->getMainId($row,$dbname,false);
               $idkey =  $this->quickEditField[$dbname]["key"];
               $idvalue = $this->getValueByDbName($row,$idkey,false);
               $html = $this->getQuickEditShowModeHtml($mainid,$idvalue,$prefix,$dbname,$value,$methodname,$export,false);
            
               return $html;

        }

        public function getLayoutColInfoShowMode($row,$dbname,$export=false)
        {

           
            $initMethod= $this->getLayoutColInfo($dbname);
            $result = $this->getResult();
            $arr = $result[$row];
            $backup = $this->exportLayout();
            $this->resetLayout();
            $this->$initMethod($_REQUEST);
            $result =  $this->getTableHtml(null, "", false, $arr);
            $this->importLayout($backup);
            return $result;

        }

        public function getSearchMode($dbname,$src,$sql=false,$methodname=null,$defaultsearch=null,$showSearchBar=false,$searchPrefix=null)
        {
            if($defaultsearch==null)
            {   
               $defaultsearch = $this->getFormDefaultValue($dbname);

            }
           
             if($methodname==null)
             {
                $fields = $this->getSearchField();
                $methodname = $fields[$dbname]["method"];
                if($sql)
                {
                    if(isset($fields[$dbname]["searchmethod"])&&$fields[$dbname]["searchmethod"]!=null&&trim($fields[$dbname]["searchmethod"])!="")
                    {
                        $methodname = $fields[$dbname]["searchmethod"];
                    }
                }
             }
             
            $searchSign = intval(ArrayTools::getValueFromArray($src,"searchSign"));   
            $searchMapping = $this->getSearchMapping($dbname);                    
            if(($defaultsearch==null||trim($defaultsearch)=="")&&$searchMapping!=null&&trim($searchMapping)!=""&&trim($src[$searchMapping])!=null&&trim($src[$searchMapping])!="")
            {
                 $defaultsearch = $src[$searchMapping];
                                    
            }   
            return $this->showSearchShowMode($methodname,$searchSign,$dbname,$src,$sql,$defaultsearch,$showSearchBar,$searchPrefix);
        }
        
        public function modifyOnClause($sql)
        {    
            if(count($this->joinArray)>0)
            {
                $quickSql = new QuickSql($sql);
                foreach($this->joinArray as $dbname => $arr)
                {
                    $joinMark = $arr["joinMark"];
                    $clause = $arr["clause"];
                    $quickSql->modifyOnClause($joinMark,$clause);   
                }
                $sql = $quickSql->getSql();
            }
            return $sql;
        }

        public function getWhereSql($src,$modifySql=false,$searchPrefix=null)
        {

            $result = "";
            $having = "";
            if($searchPrefix==null||trim($searchPrefix)=="")
            {
                $searchPrefix = $this->getSearchPrefix();
            }
            
            $fields = $this->getSearchField();
            $groups = $this->getSearchGroup();
            $defaultValue = "";
            $customSearchWhere = "";
            $customSearchMethod = $this->getCustomSearchMethod();
            $skipWhere = false;
            $skipHaving = false;
            foreach($customSearchMethod as $key=>$info)
            {
                 if($src[$key]!=null&&trim($src[$key])!="")
                 {
                    $method = trim($customSearchMethod[$key]["method"]);
                    if(!$skipWhere)
                    {
                        $skipWhere = $customSearchMethod[$key]["skipWhere"];
                    }
                    if(!$skipHaving)
                    {
                        $skipHaving = $customSearchMethod[$key]["skiphaving"];
                    }
                    $this->addHidden($key,trim($src[$key]));
                    $tmp = $this->$method($src,$key);
                    if(is_string($tmp)&&trim($tmp)!="")
                    {
                        $customSearchWhere .= "AND ".$tmp." ";
                    }
                 }
            }

            $customSearchWhere = ltrim($customSearchWhere,"AND ");
            $fullWhere = " WHERE 1=1  ";
            $linkDataSign = Array();
            $colInfo = $this->getColInfo();
            if(!$skipWhere)
            {
                foreach($groups as $groupid => $groupinfo)
                {
                       $groupRelation = $this->getSearchGroupRelation($groupid);
                
                       $tmpresult = "";
                       $tmphaving = "";
                        foreach($groupinfo as $dbname => $dbsetting)
                        {
                            $oridbname = null;
                            if(isset($fields[$dbname]["oridbname"]))
                            {
                                $oridbname = $fields[$dbname]["oridbname"];
                            }
                            if(($oridbname!=null&&trim($oridbname)!="")||(isset($colInfo[$dbname])&&$colInfo[$dbname]!=null&&trim($colInfo[$dbname])!=""))
                            {
                                $relation = ArrayTools::getValueFromArray($dbsetting,"relation");
                                $value = ArrayTools::getValueFromArray($fields,$dbname);
                                $isSql = ArrayTools::getValueFromArray($value,"issql");
                                $isHaving = ArrayTools::getValueFromArray($value,"having"); 
                                $isJoin = ArrayTools::getValueFromArray($value,"join");  
                                if($isSql)
                                {

                                    $sign =  $searchPrefix.$dbname;
                                    $end =$sign."_end"; 
                                    
                                    if(!isset($src[$sign]))
                                    {

                                        $defaultValue = $this->getFormDefaultValue($dbname);
                                        $src[$sign] = $defaultValue;
                                    } 
                                     $searchMapping = $this->getSearchMapping($dbname);
                                    
                                     if(($defaultValue==null||trim($defaultValue)=="")&&$searchMapping!=null&&trim($searchMapping)!=""&&trim($src[$searchMapping])!=null&&trim($src[$searchMapping])!="")
                                     {

                                        $defaultValue = $src[$searchMapping];
                                        $src[$sign] = $src[$searchMapping];
                                     }   
                                    if((isset($src[$sign])&&$src[$sign]!=null&&(is_array($src[$sign])||trim($src[$sign])!=""))||(isset($src[$end])&&$src[$end]!=null&&trim($src[$end])!="")||$this->getSearchMode($dbname,$src,false,null,null,false,$searchPrefix)==null)
                                    {
                                         $dstdbname = $this->getSearchFieldMapping($dbname);
                                         if($dstdbname == null || trim($dstdbname) == "")
                                         {
                                            $dstdbname  = $dbname;
                                         }
                                        $field = $this->getFieldByName($dstdbname);
                                        $islinkfield = $field["islinkfield"];
                                        if($islinkfield)
                                        {
                                            $datasign = $field["linkdatasign"];
                                            if(!in_array($datasign, $linkDataSign))
                                            {
                                                $linkDataSign[] = $datasign;
                                            }
                                        }
                                        $temp  = $this->getSearchMode($dbname,$src,true,null,null,false,$searchPrefix);
                                        if(is_string($temp)&&trim($temp)!="")
                                        {
                                            if($isHaving)
                                            {
                                                if(trim($tmphaving)!="")
                                                {

                                                    $tmphaving .= " ".$relation." ";
                                                }
                                                
                                                $tmphaving .= $this->getSearchMode($dbname,$src,true,null,null,false,$searchPrefix);
                                            }
                                            else
                                            {
                                                $part = "";
                                                if(trim($tmpresult)!=""||$isJoin)
                                                {
                                                    $part .= " ".$relation." ";
                                                }
                                                $part .= $this->getSearchMode($dbname,$src,true,null,null,false,$searchPrefix);  
                                                if($isJoin)
                                                {
                                                    $this->joinArray[$dbname] = Array("joinMark"=>$value["joinMark"],"clause"=>$part);
                                                }
                                                else
                                                {
                                                    $tmpresult.=$part;
                                                } 
                                            }
                                        }

                                    }
                                }
                                else
                                {
                                    $this->getSearchMode($dbname,$src,true,null,null,false);
                                }
                            }
                            
                        }
                        if(trim($tmpresult)!="")
                        {
                            $result .= " ".$groupRelation." ( ".$tmpresult." ) ";
                        }
                        if(trim($tmphaving)!="")
                        {
                            $having .= " ".$groupRelation." ( ".$tmphaving." ) ";
                        }

                }
          

            
                $whereClause =  $this->getWhereClause();
                if($whereClause!=null&&trim($whereClause)!="")
                {
                   $fullWhere .= " AND ".$whereClause." ";
                }
                if(strlen($result)>0)
                {   
                     $fullWhere .= $result;
                }
                if($customSearchWhere!=null&&trim($customSearchWhere)!="")
                {
                    $fullWhere .= " AND ( ".$customSearchWhere." ) ";
                }
            }
            else
            {
                if($customSearchWhere!=null&&trim($customSearchWhere)!="")
                {
                    $fullWhere .= " AND ( ".$customSearchWhere." ) ";
                }
                $newFields = Array();
                $fields = $this->getFields();
                foreach($fields as $dbname => $arr)
                {
                    $having = $arr["having"];
                    if(!$having)
                    {
                        $arr["defaultsearch"] = "";
                    }
                    $newFields[$dbname] = $arr;
                }
                $this->setFields($newFields);
            }
            if(!$skipHaving)
            {
                $fullHaving = $this->getHaving();
                if($having!=null&&trim($having)!="")
                {
                    if($fullHaving==null||trim($fullHaving)=="")
                    {
                        $fullHaving = " 1=1 ";
                    }
                    $fullHaving .= $having;
                    $this->setHaving($fullHaving);
                }
            }
            else
            {
                $this->setHaving("");
                 $newFields = Array();
                foreach($fields as $dbname => $arr)
                {
                    $having = $arr["having"];
                    if($having)
                    {
                        $arr["defaultsearch"] = "";
                    }
                    $newFields[$dbname] = $arr;
                }
                $this->setFields($newFields);
            }
            if($modifySql)
            {
                $this->modifySqlForLinkData($linkDataSign);
            }
            $mainIdOriDbName  =$this->getMainIdOriDbName();
            if(isset($src["qp_keeprowsids"])&&$src["qp_keeprowsids"]!=null&&trim($src["qp_keeprowsids"])!="")
            {
                $fullWhere = $fullWhere." AND ".$mainIdOriDbName . " IN (".trim($src["qp_keeprowsids"]).") ";
            }
            if(isset($src["qp_excluderowsids"])&&$src["qp_excluderowsids"]!=null&&trim($src["qp_excluderowsids"])!="")
            {
                $fullWhere =  $fullWhere." AND ".$mainIdOriDbName . " NOT IN (".trim($src["qp_excluderowsids"]).") ";
            }
            return $fullWhere;
        } 

         public function getOrderSql()
         {
                $result = "";
                $fields = $this->getOrderField();
                foreach($fields as $dbname => $ordertype)
                {  
                    $colname = $this->getOriDbName($dbname);
                    if(trim($result)!="")
                    {
                      $result .=" , "; 
                    }
                    if($colname!=null)
                    {
                      $result .=$colname." ".$ordertype;
                    }
                }
                if($result!=null&&trim($result)!="")
                {    
                    $result = " ORDER BY ".$result;
                }
                return $result;
         }

         public  function getGroupSql()
         {
                $result = "";
                $fields = $this->getGroupField();

                foreach($fields as $dbname=>$grouptype)
                {
                    $colname = $this->getOriDbName($dbname);
                    if(trim($result)!="")
                    {
                      $result .=" , "; 
                    }
                    $result .= $colname;
                }
                if(trim($result)!="")
                {    
                    $result = " GROUP BY ".$result;
                }
                return $result;
         }

        public  function getHavingSql()
        {
            $result = "";
            $tmp = $this->getHaving();
            if($tmp!=null&&trim($tmp)!="")
            {
                $result = " HAVING ".$tmp." ";
            }
            
            return $result;
        }

         public function advanceSearchShowMode($dbname,$colname,$src,$sql=false,$defaultValue="")
        {
               
                $sign = $this->getSearchPrefix().$dbname;  
                $value = $src[$sign];
                $advanceSearchParentID = $this->getAdvanceSearchParentID();
                $id = StringTools::getRandStr();
                if($advanceSearchParentID!=null&&trim($advanceSearchParentID)!="")
                {
                    $sign = $this->getAdvanceSearchPrefix().$advanceSearchParentID."_".$dbname;
                    $value = $src[$sign];
            
                }
    
                if($sql)
                {    
                    $valueSrc = CommonTools::getDataArray($src,$this->getAdvanceSearchPrefix().$value."_",false);
                    $this->setAdvanceSearchParentID($value);  
                        $resultSql = " ";
                        if(count($valueSrc)>0)
                        {
                           $where  = $this->getWhereSql($src,false,$this->getAdvanceSearchPrefix().$value."_");
                           if(trim($where) !="WHERE 1=1")
                           {
                                $resultSql = " ".$colname." ".$src["searchtype_".$this->getAdvanceSearchPrefix().$value]. " ( ".$this->getCustomSqlForAdvanceSearch($dbname).$this->getWhereSql($src,false,$this->getAdvanceSearchPrefix().$value."_").$this->getGroupSql().$this->getHavingSql().")";
                           }
                           
                            //echo $this->getWhereSql($src,false,$this->getAdvanceSearchPrefix().$value."_");
                        }
                    return $resultSql;
                }
                if($value!=null&&trim($value)!="")
                {
                    $id = $value;

                }  
                return $this->getAdvanceSearchHtml($src,$dbname,$sign,$id);
        } 

        public function  getSearchBarHtml($src=null,$showSearchBar=false,$showCustomCol=true)
        {
            if($src==null)
            {
                $src = $_REQUEST;
            }
            $html = "";
            if($this->getSearchBar())
            { 
                $searchGroup = $this->getSearchGroup();
                
                foreach($searchGroup as $groupid => $searchSetting)
                {
                       
                        $groupName = $this->getSearchGroupName($groupid);
                        $rowHtml="<tr>";
                        if($groupName!=null&&trim($groupName)!="")
                        {
                            $rowHtml.="<td>".$groupName.":</td>";
                        }
                        $rowHtml.="<td>";
                        $count = count($searchSetting);
                        $count2 = 0;
                        $rowHtml.='<div class="row">';
                        foreach($searchSetting as $dbname=>$setting)
                        {
                            if($this->getColStatus($dbname))
                            {   
                                 $text = $setting["text"];
                                if($text!=null&&trim($text)!="")
                                {
                                  $count2 += 2;
                                }
                            }
                        }
                        $width = intval((12-$count2)/$count);
                        foreach($searchSetting as $dbname=>$setting)
                        {
                             $tmp = "";
                             $needAdd = false;
                             if($showCustomCol||$dbname!=$this->getCustomColMark())
                             {
                                $needAdd = true;
                                $text = $setting["text"];
                                if($text!=null&&trim($text)!="")
                                {
                                   $tmp.='<div align="center" class="col-md-2">';
                                   $tmp.= $text;
                                   $tmp.='</div>';
                                }
                                $tmp.='<div  class="col-md-'.$width.'">';
                                $tmp.=$this->getSearchMode($dbname,$src,false,null,null,$showSearchBar);
                                $tmp.='</div>';
                                $tmp.='</div>';
                            }
                            if($needAdd)
                            {
                                $rowHtml.=$tmp;
                            }
                        }  
                        $rowHtml.="</td></tr>";
                        if($needAdd)
                        {
                          $html.=  $rowHtml; 
                        }
                }
            }
            return $html;
        }

        public function getAdvanceSearchHtml($src,$dbname,$sign,$id)
        { 
              $text  = "Loading...";
              

              if($id!=null&&trim($id)!="")
              {

                 $searchPrefix = $this->getAdvanceSearchPrefix().$id."_";
                 $this->setAdvanceSearchParentID($id);
                 $this->setSearchPrefix($searchPrefix);
                 $array = CommonTools::getDataArray($src,$searchPrefix,false);
                 if(count($array)>0)
                 {
                    $typeSelectId = "searchtype_".$this->getAdvanceSearchPrefix().$id;
                    $typeSelect = new HtmlElement($typeSelectId,$typeSelectId);
                    $typeSelect->setParam("class","form-control");
                    $typeArray = Array("Include"=>"IN","Not Include"=>"NOT IN");
                    $typeValue = $src[$typeSelectId];
                    $text = "<table width='100%'>";
                    $text.="<tr><td>Search Type</td><td><div class='row'><div class='col-md-12'>".$typeSelect->getSelect($typeArray,$typeValue)."</div></div>;</td></tr>";
                    $text.= $this->getSearchBarhtml($src,false,false);
                    $text.="</table>";
                 }
              }
              $html = '<input type="hidden" id="'.$sign.'" name="'.$sign.'" value="'.$id.'" />';
              $html .= '<div class="panel panel-default">
                    <div class="panel-heading" role="tab" id="headingTwo" >
                      <h4 class="panel-title">
                        <a id="click_'.$id.'" name="click_'.$id.'" class="collapsed" data-toggle="collapse" data-parent="#accordion" href="#'.$id.'" aria-expanded="false" aria-controls="collapseTwo" onClick="if($(\'#show_'.$id.'\').html()==\'Loading...\'){$.post(\''.QuickFormConfig::$quickFormMethodPath."advanceSearchBar.php".'\',{parent:\''.$id.'\',parent:\''.$id.'\',formmark:\''.$this->getFormMark().'\',isreport:\''.$this->isReport().'\'},function(data,status){$(\'#show_'.$id.'\').html(data);});}">
                            Set
                        </a>
                      </h4>
                    </div>
                    <div id="'.$id.'" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingTwo">
                      <div id="show_'.$id.'" class="panel-body">'.$text.'</div>
                    </div>
                  </div>';
               
            return $html;
        }


    public function createExportObj($withTitle=true)
    {
        $mapping = Array();
        $titleData = Array();
        $quickExcel = new QuickExcel();
        $w = -1;
        $fields = $this->getFieldArray($this->exportField);
        foreach( $fields as $dbname=>$method)
        {
                $fieldinfo =  $this->fields[$dbname];
                $w ++;
                $colMark = $quickExcel->getColMark($w);
                $mapping[$colMark] = $dbname;
                $titleData[$dbname] = $fieldinfo["displayname"];
        }
        $exportData = Array();
        if($withTitle)
        {
            $exportData[] = $titleData;
        }
        $data = Array();
        for($i=0;$i<$this->getResultSize();$i++)
        {
            $d = Array();
            foreach($mapping as $colMark=>$dbname)
            {
                $d[$dbname] =  $this->getValueByDbName($i,$dbname,true,true);   
            }
            $data[] = $d;
            $exportData[] = $d;
        }
        
        if($withTitle)
        {
           $tmp = Array($titleData);
        
        }
        if($this->isExportMainData)
        {
            $quickExcel->setCellDataFromArray($exportData,$mapping,1,null,$this->exportImageSetting);
        }
        $quickExcel = $this->customExport($quickExcel,$mapping,$data,$withTitle,$titleData);
    

        return $quickExcel->getExcelData();
    }

     public function createTitleCsv($getArray= false)
     {
        $titleStr = "";
        $fields = $this->getFieldArray($this->exportField);
        $array = Array();
        foreach($fields as $dbname=>$method)
        {
              $fieldinfo =  $this->fields[$dbname];

             
                $value = $fieldinfo["displayname"];

                $value = strip_tags(str_replace("\"","\"\"",$value));  
                 if($this->oriCsv||$getArray)
                 {
                         $dataStr.= ",".$value;
                         $array[] = $value;
                 }
                 else
                 {
                         $titleStr.= ",\"".$value."\"";
                         
                 }      
        }

        $titleStr = substr($titleStr, 1)."\n";
        if($getArray)
        {
            $titleStr = $array;
        }
        return $titleStr;
     }

  }
   
?>