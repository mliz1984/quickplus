<?php
namespace Quickplus\Lib;
use Quickplus\Lib\Tools\ArrayTools;
use Quickplus\Lib\Tools\DbTools;
use Quickplus\Lib\Tools\FileTools;
use Quickplus\Lib\Tools\UrlTools;
use Quickplus\Lib\Tools\StringTools;
use Quickplus\Lib\Tools\HtmlElement;
use Quickplus\Lib\Tools\jqueryTools;
use Quickplus\Lib\DataMsg\DataMsg;


    class reportDesignerBase extends categoryReport
    {

         protected $loginCheck =true;
         protected $mainIdCol = null;
         protected $voidBootStrap = Array();
         protected $isAddMode = true; 
         protected $deteleValidateMethod = null;
         protected $fileInSetModeArray;
         protected $savedFields;       
         protected $userSearchShowMode;
         protected $userEditShowMode;
         protected $userExportShowMode;
         protected $userReportShowMode;
         protected $defaultearchShowMode;
         protected $defaultExportShowMode; 
         protected $defaultEditShowMode;
         protected $defaultReportShowMode; 
         protected $fields;
         protected $fieldsOrder;
         protected $editInputButton = Array();
         protected $searchPrefix = "si_";
         protected $editPrefix = "ei_"; 
         protected $uploadPrefix = "ui_";
         protected $quickEditPrefix = "qei_";
         protected $extendTablePrefix = "et_";
         protected $listTablePrefix = "lt_";
         protected $listTable = Array();
         protected $isSearch = 0;
         protected $jsFile= Array();
         protected $cssFile = Array();
         protected $methodArray;
         protected $layoutHidden;
         protected $viewTopHtml;
         protected $viewButtomHtml;    
         protected $viewButtonHtml;
         protected $editTopHtml;
         protected $editButtomHtml;
         protected $editButtonHtml;
         protected $layoutButtonHtml;
         protected $layoutBottomHtml;
         protected $layoutTopHtml;
         protected $buttomHtml;
         protected $toolbar;
         protected $pageexport;
         protected $export;
         protected $clearButton = true;
         protected $methodResult = null;
         protected $searchBar = true; 
         protected $editDefaultValueMap = null;
         protected $colinfo = Array();
         protected $sql = null;
         protected $isExportMode = false;
         protected $isExportWithTitle = true;
         protected $attrArray = array();
         protected $exportFileName = "export";
         protected $isExport = false;
         protected $isSeq = true;
         protected $checkRule = Array();
         protected $attachData = Array();
         protected $exportField = Array();
         protected $exportFileField = Array();
         protected $reportField = Array();
         protected $searchField = Array();
         protected $layoutField = Array();
         protected $importField = Array();
         protected $havingString = null;
         protected $groupField = Array();
         protected $orderField = Array();
         protected $quickEditField = Array();
         protected $detailField = Array();
         protected $Field = Array();
         protected $editField = Array();
         protected $editHiddenField = Array();
         protected $editCustomField = Array();
         protected $methodWarning = Array();
         protected $methodSuccess = Array();
         protected $spanAttributes = Array();
         protected $tipCol = Array();
         protected $tranColMapping = Array();
         protected $uploaderMapping = Array();
         protected $customInitMapping = Array();
         protected $validateRulesMapping = Array();
         protected $editColMapping = Array();
         protected $initMethod = "init";
         protected $initEditMethod = "initEdit";
         protected $template = Array();
         protected $reportTemplate = Array();
         protected $exportTemplate = Array();
         protected $searchTemplate = Array();
         protected $editTemplate = Array();
         protected $valueMark = "<--QuickForm ValueMark-->";
         protected $checkBoxesSetting = Array();
         protected $editFieldLink = Array();
         protected $isReport = false;
         protected $searchBarCollapse = false;
         protected $formMark = null; 
         protected $editFormId = "editData";
         protected $whereGroup = Array();
         protected $exportMethod = Array();
         protected $searchFieldMapping =Array();
         protected $searchGroup = Array();
         protected $searchGroupSetting = Array();
         protected $searchMapping = Array();
         protected $sqlGroup = Array();
         protected $customSearchMethod = Array();
         protected $exportFormat = Array("CSV"=>false,"XLS"=>true,"XLSX"=>true);
         protected $exportFormatMethod = Array("CSV"=>"exportCsv","XLS"=>"exportXls","XLSX"=>"exportXlsx");
         protected $exportZipFormatMethod = Array("CSV"=>"exportCsvZip","XLS"=>"exportXlsZip","XLSX"=>"exportXlsxZip");
         protected $defaultExportFormat = "CSV";
         protected $linkData = Array();
         protected $linkInfo = Array();
         protected $linkPath = Array();
         protected $attachJsArray = Array();
         protected $finalWhereClause = null;
         protected $customProcessMethod = Array();
         protected $multiLineSetting = Array();
         protected $tableWidth = defaultTableWidth;
         protected $allowWrap  = defaultTableAllowWrap;
         protected $fullScreenMode = defatltFullScreenMode;
         protected $methodSuccessString = null;
         protected $methodFailString = "Operation failed, please check your data.";
         protected $passwordMinLength = null;
         protected $passwordFieldMapping = Array();
         protected $editCustomHtml =Array();
         protected $isShowChoose = true;
         protected $extendTable = Array();
         protected $extendTableCol =Array();
         protected $extendTableData = Array();
         protected $extendTableHiddenCol = Array();
         protected $extendSetting = Array();
         protected $packageCol = Array();
         protected $smartInputSetting = Array();
         protected $editorSetting = Array();
         protected $colSetting = Array();
         protected $magicFunction = Array();
         protected $commonSelectOption = Array();
         protected $commonSelectFunction = Array();
         protected $switchTextArray = Array();
         protected $searchDivMode = defaultSearchDivMode;
         protected $advanceSearchPrefix = "asi_";
         protected $advanceSearchParentID = null;
         protected $dateFormatSetting = Array();
         protected $numberFormatSetting = Array();
         protected $treeData = Array();
         protected $defaultInfoMethod = null;
         protected $extendInfoMethod = Array();
         protected $customJs = Array();
         protected $customOperationButtons = Array();
         protected $ajaxTreeSetting = Array();
         protected $multiDatePickerSetting = Array();
         protected $timeZoneSetting = Array();
         protected $attachHtml = Array();
         protected $dataSrc = null;
         protected $ajaxCustomData = Array();

         public function setAjaxCustomData($dbname,$key,$value,$isStaticValue=false)
         {
            $this->ajaxCustomData[$dbname][$key] = Array("isStaticValue"=>$isStaticValue,"value"=>$value);
         }

         public function getAjaxCustomDataStr($dbname,$isJquery=true)
         {
         
            $ret = "";
            if(is_array($this->ajaxCustomData[$dbname])&&count($this->ajaxCustomData[$dbname])>0)
            {
               foreach($this->ajaxCustomData[$dbname] as $key => $data)
               {
                  $isStaticValue = $data["isStaticValue"];
                  $value = $data["value"];
                  $ret.="&".$key."=";
                  if($isStaticValue)
                  {
                     $ret.=$value;
                  }
                  else
                  {
                     if($isJquery)
                     {
                        $ret.="'+$(\"#".$value."\").val()+'"; 
                     }
                     else
                     {
                        $ret .= "\"+document.getElementById('".$value."').value+\"";
                     }
                  }
               }
            }
           
            return $ret;
         }
         
         public function setNumberFormatSetting($dbname,$min=null,$max=null,$step=1)
         {
            $this->numberFormatSetting[$dbname] = Array("min"=>$min,"max"=>$max,"step"=>$step);
         }

         public function getNumberFormatSetting($dbname)
         {
             $ret  = Array("min"=>null,"max"=>null,"step"=>1);
             if(isset($this->numberFormatSetting[$dbname]))
             {
               $ret =  $this->numberFormatSetting[$dbname];
             }
            
             return $ret;
         }
        
         public function setDataSrc($src)
         {
            $this->dataSrc = $src;
         }
         public function getDataSrc()
         {
            $src = $_REQUEST;
            if($this->dataSrc!=null)
            {
               $src = $this->dataSrc;
            }
            return $src;
         }
         public function setAttachHtmlForSearchField($dbname,$html,$searchField=true)
         {

             if($searchField)
             {
                  $this->attachHtml[$dbname]["search"] = $html;
             }

         }
         public function setTimeZone($dbTimeZone,$pageTimeZone)
         {
            if($dbTimeZone!=$pageTimeZone)
            {
               $this->timeZoneSetting["common"]["feature"] = true;
               $this->timeZoneSetting["common"]["dbTimeZone"] = $dbTimeZone;
               $this->timeZoneSetting["common"]["pageTimeZone"] = $pageTimeZone;
            }
            else
            {
               $this->timeZoneSetting["common"]["feature"] = false;
            }
         }

         public function setTimeZoneByField($dbname,$dbTimeZone,$pageTimeZone)
         {
             if($dbTimeZone!=$pageTimeZone)
            {
               $this->timeZoneSetting["field"][$dbname]["feature"] = true;
               $this->timeZoneSetting["field"][$dbname]["dbTimeZone"] = $dbTimeZone;
               $this->timeZoneSetting["field"][$dbname]["pageTimeZone"] = $pageTimeZone;
            }
            else
            {
               $this->timeZoneSetting["field"][$dbname]["feature"] = false;
            }
         }
         protected function getTimeForShow($dbname,$value,$fm,$forShow=true)
         {
            if($value!=null&&trim($value)!="")
            {
               $feature = false;
               $dbTimeZone = null;
               $pageTimeZone = null;
               if(isset($this->timeZoneSetting["common"]["feature"])&&is_bool($this->timeZoneSetting["common"]["feature"])&&$this->timeZoneSetting["field"]["common"]["feature"])
               {
                  $feature = true;
                  if(isset($this->timeZoneSetting["common"]["dbTimeZone"])&&$this->timeZoneSetting["common"]["dbTimeZone"]!=null&&trim($this->timeZoneSetting["common"]["dbTimeZone"])!=""&&isset($this->timeZoneSetting["common"]["pageTimeZone"])&&$this->timeZoneSetting["common"]["pageTimeZone"]!=null&&trim($this->timeZoneSetting["common"]["pageTimeZone"])!="")
                  {
                      $dbTimeZone = trim($this->timeZoneSetting["common"]["dbTimeZone"]);
                      $pageTimeZone = trim($this->timeZoneSetting["common"]["pageTimeZone"]);
                  }
               }
               
               if(isset($this->timeZoneSetting["field"][$dbname]["feature"]))
               {
                  if(is_bool($this->timeZoneSetting["field"][$dbname]["feature"])&&$this->timeZoneSetting["field"][$dbname]["feature"])
                  {
                     $feature = true;
                     if(isset($this->timeZoneSetting["field"][$dbname]["dbTimeZone"])&&$this->timeZoneSetting["field"][$dbname]["dbTimeZone"]!=null&&trim($this->timeZoneSetting["field"][$dbname]["dbTimeZone"])!=""&&isset($this->timeZoneSetting["field"][$dbname]["pageTimeZone"])&&$this->timeZoneSetting["field"][$dbname]["pageTimeZone"]!=null&&trim($this->timeZoneSetting["field"][$dbname]["pageTimeZone"])!="")
                     {
                      $dbTimeZone = trim($this->timeZoneSetting["field"][$dbname]["dbTimeZone"]);
                      $pageTimeZone = trim($this->timeZoneSetting["field"][$dbname]["pageTimeZone"]);
                     }
                  }
                  else
                  {
                     $feature = false;
                  }
               }
               if($feature&&$dbTimeZone!=null&&$pageTimeZone!=null&&trim($dbTimeZone)!=trim($pageTimeZone))
               {
                  $dateUtil = new DateUtil();
                  if($forShow)
                  {
                     $value = $dateUtil->toTimeZone($fm,$dbTimeZone,$pageTimeZone);
                  }
                  else
                  {
                     $value = $dateUtil->toTimeZone($fm,$pageTimeZone,$dbTimeZone);
                  }
               }
            }
            return $value;
         }
         public function clearSearchBar()
         {
            $this->searchField = Array();
            $this->searchGroup = Array();
            $this->searchGroupSetting = Array();
         }
         public function setMultiDatePickerMonth($dbname,$row,$col)
         {
               $this->multiDatePickerSetting[$dbname]["numberOfMonths"] = Array($row,$col);
         }
         public function setAjaxTreeSetting($key,$dataMethod,$sqlMethod=null,$idKey="id",$nameKey="name",$translateBy=null)
         {
            $this->ajaxTreeSetting[$key]["dataMethod"] = $dataMethod;
            $this->ajaxTreeSetting[$key]["sqlMethod"] = $sqlMethod;
            $this->ajaxTreeSetting[$key]["idKey"] = $idKey;
            $this->ajaxTreeSetting[$key]["nameKey"] = $nameKey;
         }
         public function setCustomJs($id,$js)
         {
            $this->customJs[$id] = $js;
         }
         public function getCustomJs($includeTag=true)
         {
            $jsStr ="";
            if(is_array($this->customJs)&&count($this->customJs)>0)
            {
                foreach($this->customJs as $id =>$js)
                {
                  $jsStr .= $js;
                }
               if($includeTag)
               {
                     $jsStr ='<script type="text/javascript">'.$jsStr.'</script>';
               }
            }

             return $jsStr;
         }
         public function setDefaultExtendInfoMethod($defaultInfoMethod)
         {
            $this->defaultInfoMethod = $defaultInfoMethod;
         }
         public function hasExtendInfo($dbname=null)
         {
            $ret = false;
            if($this->defaultInfoMethod!=null&&trim( $this->defaultInfoMethod)!="")
            {
          
               $ret = true;
            }
            else  if(($dbname==null||trim($dbname)=="")&&isset($this->extendInfoMethod)&&is_array($this->extendInfoMethod)&&count($this->extendInfoMethod)>0)
            {
             
               $ret = true;
            }
            else if($dbname!=null&&trim($dbname)!=""&&isset($this->extendInfoMethod[$dbname])&&$this->extendInfoMethod[$dbname]!=null&&trim($this->extendInfoMethod[$dbname])!="")
            {
               $ret =true;
            }
            return $ret;
         }

         public function setExtendInfoMethod($dbname,$extendInfoMethod)
         {
             $this->extendInfoMethod[$dbname] = $extendInfoMethod;
         }
         public function addTreeData($key,$data,$idKey,$nameKey,$parentKey,$topSign)
         {
            $this->addAttachData($key,$data,$idKey,$nameKey);
            $treeObject = new TreeObject($data,$idKey,$parentKey,$topSign);
            $treeObject->buildTree();
            $this->treeData[$key] = $treeObject;
        
         }
         public function addTreeDataByData($db,$key,$data,$idKey,$nameKey,$parentKey,$topSign)
         {
            $data->setDb($db);
            $dataMsg = $data->find();
            $this->addTreeData($key,$dataMsg->getDataArray(),$idKey,$nameKey,$parentKey,$topSign);
         }
         public function addTreeDataBySql($db,$key,$sql,$idKey,$nameKey,$parentKey,$topSign)
         {
            $dataMsg = new DataMsg();
            $dataMsg->findBySql($sql);
            $this->addTreeData($key,$dataMsg->getDataArray(),$idKey,$nameKey,$parentKey,$topSign);
         }
         public function setDateFormat($dbname,$dateFormat)
         {
             $this->dateFormatSetting[$dbname] = $dateFormat;
         }
         public function getDateFormat($dbname,$defaultValue=null)
         {
               $ret = $defaultValue;
               if(isset( $this->dateFormatSetting[$dbname])&& $this->dateFormatSetting[$dbname]!=null&&trim( $this->dateFormatSetting[$dbname])!="")
               {
                   $ret =  $this->dateFormatSetting[$dbname];
               }
               return $ret;
         }
         public function setAdvanceSearchParentID($advanceSearchParentID)
         {
            $this->advanceSearchParentID = $advanceSearchParentID;
         }

         public function getAdvanceSearchParentID()
         {
            return $this->advanceSearchParentID;
         }


         public function getAdvanceSearchPrefix()
         {
            return $this->advanceSearchPrefix;
         }
         public function setAdvanceSearchPrefix($advanceSearchPrefix)
         {
            $this->advanceSearchPrefix = $advanceSearchPrefix;
         }
         public function getSearchDivMode()
         {
            return $this->searchDivMode;
         }
         public function setSearchDivMode($searchDivMode)
         {
            $this->searchDivMode = $searchDivMode;
         }
         public function setPlaceHolder($dbname,$placeOrder)
         {  
            $this->colSetting[$dbname]["placeholder"] = $placeOrder;

         }
         
         public function setColStatus($dbname,$status)
         {
            $this->colSetting[$dbname]["status"] = $status;
         }
         public function getSearchGroupStatus($groupid)
         {
            $result = false;
            foreach($this->searchGroup[$groupid] as $dbname => $groupInfo)
            {
                if($this->getColStatus($dbname))
                {
                    $result = true;
                    break;
                }
            }
            return $result;

         }
         public function getColStyle($row,$dbname)
         {
            return null;
         }
         public function setColSetting($dbname,$key,$value)
         {
            $this->colSetting[$dbname][$key] = $value;
         }

         public function getColStatus($dbname)
         {
            $result = true;
            if(isset($this->colSetting[$dbname]["status"])&&is_bool($this->colSetting[$dbname]["status"]))
            {
                $result = $this->colSetting[$dbname]["status"];
            }
            return $result;
         }
         public function setColClass($dbname,$class)
         {
            $this->colSetting[$dbname]["class"] = $class;
         }
         public function getColClass($dbname)
         {
            $result = null;
            if(isset($this->colSetting[$dbname]["class"])&&$this->colSetting[$dbname]["class"]!=null&&trim($this->colSetting[$dbname]["class"])!="")
            {
                $result = trim($this->colSetting[$dbname]["class"]);
            }
            return $result;
         }
         public function setSwitchText($dbname,$onText,$offText)
         {
            $this->switchTextArray[$dbname] = Array("onText"=>$onText,"offText"=>$offText);
         }
         public function getSwitchText($dbname)
         {
            $result = Array("onText"=>null,"offText"=>null);
            if($this->switchTextArray[$dbname]["onText"]!=null&&trim($this->switchTextArray[$dbname]["onText"])!="")
            {
                $result["onText"] = $this->switchTextArray[$dbname]["onText"];
            }
            if($this->switchTextArray[$dbname]["offText"]!=null&&trim($this->switchTextArray[$dbname]["offText"])!="")
            {
                $result["offText"] = $this->switchTextArray[$dbname]["offText"];
            }
            return $result;
         }
         public function setCommonSelectFunction($dbname,$key,$value)
         {
            $this->commonSelectFunction[$dbname][$key] = $value;
         }
         public function setCommonSelectOption($dbname,$key,$value)
         {
            $this->commonSelectOption[$dbname][$key] = $value;
         }

         public function getCommonSelectOption($dbname)
         {
            $result = Array();
            if(isset($this->commonSelectOption[$dbname])&&is_array($this->commonSelectOption[$dbname]))
            {
                $result = $this->commonSelectOption[$dbname];
            }
            return $result;
         }
         public function regMagicFunction($magicFunction,$commonFunction)
         {
            $this->magicFunction[$magicFunction] = $commonFunction;
         }
         public function setEditorHeight($dbname,$height)
         {
            $this->editorSetting[$dbname]["height"] =strval($height);
         }
         public function setEditorWidth($dbname,$width)
         {
            $this->editorSetting[$dbname]["width"] =strval($width);
         }
         public function getEditorHeight($dbname)
         {
            $result = null;
            if($this->editorSetting[$dbname]["height"]!=null&&trim($this->editorSetting[$dbname]["height"])!="")
            {
                 $result  = $this->editorSetting[$dbname]["height"];
            }
            return $result;
         }
         public function getEditorWidth($dbname)
         {
            $result = null;
            if($this->editorSetting[$dbname]["width"]!=null&&trim($this->editorSetting[$dbname]["width"])!="")
            {
                 $result  = $this->editorSetting[$dbname]["width"];
            }
            return $result;
         }

         public function setListTablePrefix($listTablePrefix)
         {
            $this->listTablePrefix = $listTablePrefix;
         }
         public function getListTablePrefix()
         {
            return $this->listTablePrefix;
         }
         public function getListTable()
         {
            return  $this->listTable;
         }
         public function setListTable($id,$tabletitle,$dbname,$listTableName,$listTableId,$listKey=null)
         {
            $lkey = $dbname;
            if($listKey!=null&&trim($listKey)!="")
            {
                $lkey = $listKey;
            }
            $this->listTable[$id] = Array("tabletitle"=>$tabletitle,"dbname"=>$dbname,"listTableName"=>$listTableName,"listTableId"=>$listTableId,"listKey"=>$lkey);
         }
         public function setListTableWhereClause($id,$whereClause)
         {
            if(is_array($this->listTable[$id]))
            {
                 $this->listTable[$id]["whereClause"] = $whereClause;
            }
         }
         public function setListTableNameMethod($id,$nameMethod)
         {
            if(is_array($this->listTable[$id]))
            {
                $this->listTable[$id]["nameMethod"] = $nameMethod;
            }
         }
          public function setListTableIdMethod($id,$idMethod)
         {
            if(is_array($this->listTable[$id]))
            {
                $this->listTable[$id]["idMethod"] = $idMethod;
            }
         }
         public function setListTableData($id,$dsttitle,$dstdbname,$array,$idKey,$nameKey)
         {
            if(is_array($this->listTable[$id]))
            {
                $this->listTable[$id]["data"] = Array("dsttitle"=>$dsttitle,"dstdbname"=>$dstdbname,"array"=>$array,"idKey"=>$idKey,"nameKey"=>$nameKey,"nameMethod"=>null,"idMethod"=>null);
            }
           
         }
        public function setListTableCol($id,$title,$col,$customMethod,$isHtml=false)
         {

            if(is_array($this->listTable[$id]))
            {
                $this->listTable[$id]["cols"][$col] = Array("title"=>$title,"customMethod"=>$customMethod,"isHtml"=>$isHtml);
            }
         }
         public function getListTableHtml($id,$dataArray)
         {
            $html = "";
            if(is_array($this->listTable[$id]))
            {
                $array = $this->listTable[$id]["data"]["array"];
                $cols = $this->listTable[$id]["cols"];
                if(is_array($array)&&count($array)>0&&is_array($cols)&&count($cols)>0)
                {
                    $dbname = $this->listTable[$id]["dbname"];
                    $oriData = null;
                    $listKey = $this->listTable[$id]["listKey"];
                    $value =$dataArray[$dbname]; 
                    $hiddenvalue = "";
                    $listTableId = $this->listTable[$id]["listTableId"];
                    $dstdbname = $this->listTable[$id]["data"]["dstdbname"];
                    if($value!=null&&trim($value)!="")
                    {
                        $hiddenvalue = $value;
                        $db = $this->getDb();
                        $listTableName = $this->listTable[$id]["listTableName"];
                        $whereClause = $listKey." = '".$value."' ";
                        if($this->listTable[$id]["whereClause"]!=null&&trim($this->listTable[$id]["whereClause"])!="")
                        {
                            $whereClause.=" AND ".$this->listTable[$id]["whereClause"];
                        }
                        $data = new Data($db,$listTableName,$listTableId);
                        $data->setWhereClause($whereClause);

                        $dataMsg = $data->find();
                        
                        if($dataMsg->getSize()>0)
                        {
                            $oriData = $dataMsg->getKeyDataArray($dstdbname,true,true);
                        }
                    }
                 
                    $dsttitle = $this->listTable[$id]["data"]["dsttitle"];
                    $dstdbname = $this->listTable[$id]["data"]["dstdbname"];
                    $idKey = $this->listTable[$id]["data"]["idKey"];
                    $nameKey = $this->listTable[$id]["data"]["nameKey"];
                    $nameMethod = $this->listTable[$id]["data"]["nameMethod"];
                    $idMethod = $this->listTable[$id]["data"]["idMethod"];
                    $listTablePrefix = $this->getListTablePrefix();
                    $hiddenid = $listTablePrefix.$id."_".$listKey;
                    $hidden = new HtmlElement($hiddenid,$hiddenid);
                    $html.=$hidden->getHidden($hiddenvalue);
                    $html.='<table id="'.$id.'_listtable" width="100%">';
                    
                    $titleSign = false;
                    $title = "";
                    $body = "";
                    foreach($array as $a)
                    {

                        $idSign = false;
                        $body.="<tr>";
                        $name = $a[$nameKey];
                        if($nameMethod!=null&&trim($nameMethod)!="")
                        {
                            $name = $this->$nameMethod($id,$dataArray,$a);
                        }
                        $hiddenid = $listTablePrefix.$id."_".$dstdbname."[]";
                        $hidden = new HtmlElement($hiddenid,$hiddenid);
                        $hiddenvalue = $a[$idKey];
                        if($idMethod!=null&&trim($idMethod)!="")
                        {
                            $hiddenvalue = $this->$idMethod($id,$dataArray,$a);
                        }

                        $body.="<td align='center'>".$name.$hidden->getHidden($hiddenvalue)."</td>";
                        $d = Array();

                        if(is_array($oriData)&&is_array($oriData[strval($a[$idKey])]))
                        {
                            $d = $oriData[strval($a[$idKey])];
                        }
                        if(!$titleSign)
                        {
                            $title.="<tr>";
                            $title.="<td align='center'><b>".$dsttitle."</b></td>";
                        }
                        foreach($cols as $col =>$colInfo)
                        {
                            if(!$titleSign)
                            {
                               $title.= "<td align='center' ><b>".$colInfo["title"]."</b></td>";
                            }
                            if($colInfo["isHtml"])
                            {
                                $body.="<td align='center' >".$colInfo["customMethod"];
                            }
                            else
                            {

                                $searchPrefix = $this->getSearchPrefix();
                                $prefix = $listTablePrefix.$id."_";
                                $this->setSearchPrefix($prefix);
                                $method =$colInfo["customMethod"];
                                $colHtml = $this->$method($col,$col,$d,false,$d[$col]);
                                $colHtml = str_replace($prefix.$col,$prefix.$col."[]",$colHtml);
                                $body.="<td align='center'>".$colHtml;
                                $this->setSearchPrefix($searchPrefix);
                            }
                            if(!$idSign)
                            {
                                $idSign = true;
                                $hiddenid = $listTablePrefix.$id."_".$listTableId."[]";
                                $hidden = new HtmlElement($hiddenid,$hiddenid);
                                $tmpv=  "";
                                if($d[strval($listTableId)]!=null&&trim($d[strval($listTableId)])!="")
                                {
                                    $tmpv = $d[strval($listTableId)];
                                    $body.=$hidden->getHidden($tmpv);
                                }
                               
                            }
                            $body.="<td>";

                        }
                        if(!$titleSign)
                        {
                            $title.="</tr>";
                            $titleSign = true;
                        }
                        $body.="</tr>";
                    }
                    $html .=$title.$body.'</table>';
                }
            }
            return $html;
         }
       
         public function setSmartInputSetting($dbname,$tablename,$findkey,$submitJs="_search()")
         {  
            $this->smartInputSetting[$dbname] = Array("tablename"=>$tablename,"findkey"=>$findkey,"submitJs"=>$submitJs,"whereClause"=>null,"autoSubmit"=>true,"autoFocus"=>false);
         }

         public function setSmartInputWhereClause($dbname,$whereClause)
         {
            $this->smartInputSetting[$dbname]["whereClause"] = $whereClause;
         }

          public function setSmartInputAutoSubmit($dbname,$autoSubmit)
         {
            $this->smartInputSetting[$dbname]["autoSubmit"] = $autoSubmit;
         }

          public function setSmartInputAutoFocus($dbname,$autoFocus)
         {
            $this->smartInputSetting[$dbname]["autoFocus"] = $autoFocus;
         }



         public function getSmartInputSetting($dbname)
         {
            $setting = $this->smartInputSetting[$dbname];
            $tablename = $setting["tablename"];
            $findkey = $setting["findkey"];
            $whereClause = $setting["whereClause"]; 
            $db = $this->getDb();
            $dataMsg = new DataMsg();
            $sql = "SELECT  MAX(LENGTH(".$findkey.")) maxlength,MIN(LENGTH(".$findkey.")) minlength FROM ".$tablename;
            if($db->isMssql())
            {
                $sql = "SELECT  MAX(LEN(".$findkey.")) maxlength,MIN(LEN(".$findkey.")) minlength FROM ".$tablename;
            }
            if($whereClause!=null&&trim($whereClause)!="")
            {
                $sql.=" WHERE ".$whereClause;
            }
            
            $limitArray = $dataMsg->getUniData($db,$sql,true);
            $setting["maxlength"] = $limitArray["maxlength"];
            $setting["minlength"] = $limitArray["minlength"];
            return $setting;
         }

         public function setLoginCheck($loginCheck)
         {
            $this->loginCheck =  $loginCheck;
         }
         public function getLoginCheck()
         {
            return $this->loginCheck;
         }
         public function setPackageCol($packageCol,$col,$removeFromSrc=false)
         {
            $this->packageCol[$packageCol][$col] = $removeFromSrc;
         }
         public function setPackageColByDbName($packageCol,$dbname,$removeFromSrc=false)
         {
            $this->setPackageCol($packageCol,$this->getEditPrefix().$dbname,$removeFromSrc);
         }
         public function setPackageColByExtendTable($packageCol,$extendTableId,$removeFromSrc=false)
         {
            $this->setPackageCol($packageCol,$this->getExtendTablePrefix().$packageCol,$removeFromSrc);
         }

         public function getExtendTableDataInfo($id)
         {
            return $this->extendTableData[$id];
         }
         public function setExtendAddText($id,$text)
         {
            $this->extendSetting[$id]["addText"] = $text;

         }
         public function setExtendDeleteText($id,$text)
         {
            $this->extendSetting[$id]["deleteText"] = $text;
         }
         public function setExtendLoadMethod($id,$method)
         {
            $this->extendSetting[$id]["loadMethod"] = $method;
         }
         public function setExtendSaveMethod($id,$method)
         {
             $this->extendSetting[$id]["saveMethod"] = $method;
         }
         public function setExtendDeleteMethod($id,$method)
         {
            $this->extendSetting[$id]["deleteMethod"] = $method;
         }
         public function setExtendTableData($id,$tablename,$key,$linkkey,$dstdbname=null,$linkdelete=true)
         {  
            if($dstdbname==null||trim($dstdbname)=="")
            {
                $dstdbname =$this->getMainIdDbName();
            }
            $this->extendTableData[$id] = Array("id"=>$id,"tablename"=>$tablename,"key"=>$key,"linkkey"=>$linkkey,"dstdbname"=>$dstdbname,"linkdelete"=>$linkdelete);
         }

         public function setExtendTableRange($id,$col,$value,$operation=" = ")
         {
            if(is_array($this->extendTableData[$id]))
            {
                $this->extendTableData[$id]["range"][$col]["value"] = $value;
                $this->extendTableData[$id]["range"][$col]["operation"] = $operation; 
             }
         }
        


         public function setExtendTablePrefix($extendTablePrefix)
         {
            $this->extendTablePrefix = $extendTablePrefix;
         }
         public function getExtendTablePrefix()
         {
            return $this->extendTablePrefix;
         } 

         public function getExtendTable($extendTableId=null)
         {
            $result =  $this->extendTable;
            if($extendTableId!=null&&trim($extendTableId)!="")
            {
                $result = $this->extendTable[$extendTableId];
            }
            return $result;
         }


         
        public function setExtendTable($tableid,$tableTitle,$isCustomRelationMethod,$mustHaveColNum=0,$initColNum=1)
         {
            $this->extendTable[$tableid] = Array("tableid"=>$tableid,"tabletitle"=>$tableTitle,"iscustomrelationmethod"=>$isCustomRelationMethod,"musthavecolnum"=>$mustHaveColNum,"initcolnum"=>$initColNum);
         }

         public function setExtendTableEditor($tableid,$dbname,$title)
         {
            $sign = "Et".$tableid.$dbname;
            $addJs = "var ".$sign."_oldContent = new Array(); 
             $('div.".$sign."').each(function(){   
                ".$sign."_oldContent.push(UE.getEditor($(this).attr('id')).getContent());
            });
            var ".$sign."_newid='".$sign."_'+Math.random().toString(36).substr(2);$('textarea.".$sign."').last().attr('id',".$sign."_newid);$('textarea.".$sign."').last().attr('class','".$sign."');var ".$sign."_ue = UE.getEditor(".$sign."_newid);
                ".$sign."_oldContent.push('');
                 ".$sign."_ue.ready(function() {
                      var i = 0;
                      $('div.".$sign."').each(function(){
                        UE.getEditor($(this).attr('id')).setContent(".$sign."_oldContent[i]);
                        i = i + 1;
                      });
             });
            ";
            $initJs = "var ".$sign."_oldContent = new Array(); 
             var ".$sign."_ue;
             $('textArea.".$sign."').each(function(){ 
                var ".$sign."_newid='".$sign."_'+Math.random().toString(36).substr(2);
                ".$sign."_oldContent.push($(this).val())
                $(this).attr('id',".$sign."_newid);
                ".$sign."_ue = UE.getEditor(".$sign."_newid);
            });
            ".$sign."_ue.ready(function() {
                      var i = 0;
                      $('div.".$sign."').each(function(){
                        UE.getEditor($(this).attr('id')).setContent(".$sign."_oldContent[i]);
                        i = i + 1;
                      });
                 });
            ";
            $this->setExtendTableCol($tableid,$dbname,$title,"getTextAreaWithClass".$sign,false,$addJs,$initJs);
         }
         public function setExtendTableHiddenCol($tableid,$dbname,$defaultvalue="")
         {
            $this->extendTableHiddenCol[$tableid][$dbname] = $defaultvalue;  
            //echo $defaultvalue; 
         }

         public function getExtendTableHiddenCol($tableid)
         {
            return $this->extendTableHiddenCol[$tableid];
         }
         public function setExtendTableCol($tableid,$dbname,$title,$customMethod,$isHtml=false,$addjs="",$initJs="")
         {
            $this->extendTableCol[$tableid][$dbname]= Array("tableid"=>$tableid,"dbname"=>$dbname,"ishtml"=>$isHtml,"custommethod"=>$customMethod,"title"=>$title,"addjs"=>$addjs,"initjs"=>$initJs);
         }
         protected function getFixDataForExtendTable($extendTableId,$dataArray,$deleteText)
         {

                $result = Array();
                $fixDataList = Array();
                if(is_array($this->extendTableData[$extendTableId]))
                {
                    $db = $this->getDb();
                    $dataInfo = $this->extendTableData[$extendTableId];
                     if(isset($this->extendSetting[$id]["loadMethod"])&&$this->extendSetting[$id]["loadMethod"]!=null&&trim($this->extendSetting[$id]["loadMethod"])!="")
                    {   
                        $method = $this->extendSetting[$id]["loadMethod"];
                        $tmp = $this->$method($id,$dataArray,$dataInfo);
                        if(is_array($tmp))
                        {
                            $fixDataList = $tmp;
                        }
                    }
                    else
                    {
                        $tablename = $dataInfo["tablename"];
                        $pkey =  $dataInfo["key"];
                        $linkkey = $dataInfo["linkkey"];
                        $dstdbname = $dataInfo["dstdbname"];
                        $range = $dataInfo["range"];
                        $linkkeyvalue = $dataArray[$dstdbname];
                       if($linkkeyvalue!=null&&trim($linkkeyvalue)!="")
                       {
                            $data = new Data($db,$tablename,$pkey);
                            $data->set($linkkey,$linkkeyvalue);
                            if(is_array($range))
                            {
                                foreach($range as $colid =>$colinfo)
                                {
                                    $colval = $colinfo["value"];
                                    $coloperation = $colinfo["operation"];
                                    $data->setWithOperator($colid,$colval,$coloperation);
                                }
                            }
                            $data->addOrder($pkey);

                            $dataMsg = $data->find();
                            if($dataMsg->getSize()>0)
                            {
                                $fixDataList = $dataMsg->getDataArray();
                            }
                      }
                    }
                }
                else if($dataArray[$this->getExtendTablePrefix().$extendTableId]!=null&&trim($dataArray[$this->getExtendTablePrefix().$extendTableId])!=null)
                {
                    $jsonStr = $dataArray[$this->getExtendTablePrefix().$extendTableId];
                    $dataList = json_decode($jsonStr);
                    if(is_array($dataList))
                    {
                        $count = 0;
                        foreach($dataList as $key =>$value)
                        {
                            $count = count($value);
                            break;
                        }
                        for($i=0;$i<$count;$i++)
                        {
                            $tmp = Array();
                            foreach($dataList as $key =>$value)
                            {
                                $tmp[$key] = $value[$i];
                            }
                            $fixDataList[] =  $tmp; 
                        }

                    }
                }
                    for($i=0;$i<count($fixDataList);$i++)
                    {

                        $fixData = $fixDataList[$i];
                        $js ="quickextendtable_".$this->getExtendTablePrefix().$extendTableId.".row.add( [";
                        foreach($this->extendTableCol[$extendTableId] as $dbname =>$colinfo)
                        {
                           
                            $colHtml = "''";
                            $title = $colinfo["title"];
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
                                    //print_r($fixData[$dbname]."--".$method."<br>");
                                    $colHtml = $this->$method($dbname,$dbname,$src,false, $fixData[$dbname]);
                                    $colHtml = str_replace($qettp.$dbname,$this->getExtendTablePrefix().$extendTableId."[".$dbname."][]", $colHtml);
                                    $this->setSearchPrefix($searchPrefix);               
                                }
                            }
                            $js.="'".str_replace("'","\'",$colHtml)."',"; 
                        }
                        $idval = $fixData[$pkey];
                        $htmlid = $this->getExtendTablePrefix().$extendTableId."[".$pkey."][]";
                        $hidden = new HtmlElement($htmlid,$htmlid);
                        $hiddenHtml = $hidden->getHidden($idval);
                        $hiddenArray = $this->getExtendTableHiddenCol($extendTableId);
                        foreach($hiddenArray as $tkey => $tvalue) 
                        {
                             $thtmlid = $this->getExtendTablePrefix().$extendTableId."[".$tkey."][]";
                             $thidden = new HtmlElement($thtmlid,$thtmlid);
                             if(isset($fixData[$tkey])&&$fixData[$tkey]!=null&&trim($fixData[$tkey])!="")
                             {
                                $tvalue = $fixData[$tkey];
                             }
                             $hiddenHtml.=$thidden->getHidden($tvalue);
                        }
                        $js.="'".$hiddenHtml."<button class=\"btn btn-primary btn-sm\" onClick=\"if(!confirm(\'Do you want to delete this record from database?\')){return false;}$.get(\'/".QuickFormConfig::$quickFormMethodPath."deleteExtendTableRecord.php?id=".$idval."&tableid=".$extendTableId."&class=".$this->getFormMark()."\');quickextendtable_".$this->getExtendTablePrefix().$extendTableId.".row(quickextendtable_".$this->getExtendTablePrefix().$extendTableId.".row($(this).parents(\'tr\'))).remove().draw( false );return false;\">".$deleteText."</button>'";
                        $js.="]).draw();";     
                        $result[] = $js;
                    }
                
                return $result;
         }

        


        public function addExtendValidateRule($tableid,$dbname,$rule,$value) 
        {
            $this->validateRulesMapping["rules"]["'".$this->getExtendTablePrefix().$tableid."[".$dbname."][]'"][$rule]=$value;
        }

        public function getExtendValidateRule($tableid,$dbname,$rule)
        { 
           $ret =null;
           if(isset($this->validateRulesMapping["rules"]["'".$this->getExtendTablePrefix().$tableid."[".$dbname."][]'"][$rule]))
           {
             $ret = $this->validateRulesMapping["rules"]["'".$this->getExtendTablePrefix().$tableid."[".$dbname."][]'"][$rule];
           }
           return $ret;
          
        }

        public function addExtendQuickAjaxVaildateRule($tableid,$dbname,$value)
        {
                $url = QuickFormConfig::$quickFormMethodPath."quickAjaxVaildate.php";
                $data = Array();

                $tmpdata = Array();
                foreach($this->editField as $tmpdbname=>$data)
                {   
                  
                    $tmp = $this->getEditPrefix().$tmpdbname;
                    $tmpdata["quickAjax_".$tmp] = "function(){return $(\"#".$tmp."\").val();}";
            
                }
                $valueStr = trim( $valueStr,",");
                $array = Array();
                $array["url"] = $url;
                $array["type"] = 'POST';
                $array["dataType"] = 'json';
                
                $data = array_merge($data,$tmpdata);
               
                $data["quickAjaxMethod"] = $value;
                $data["formMark"] = $this->getFormMark();
                $data["isreport"] = "0";
                $data["dbname"] = $dbname;
                if($this->isReport())
                {
                     $data["isreport"] = "1";
                }
                if($_REQUEST["ed_dataid"]!=null&&trim($_REQUEST["ed_dataid"])!="")
                {
                        $data["mainid"] =  $_REQUEST["ed_dataid"];
                }
                $array["data"] =  $data;
            
            $this->validateRulesMapping["rules"]["'".$this->getExtendTablePrefix().$tableid."[".$dbname."][]'"]["quickAjax"]=$array;
        }


         
         public function setIsShowChoose($isShowChoose)
         {
            $this->isShowChoose = $isShowChoose;
         }
                public function isShowChoose()
         {
            return  $this->isShowChoose;
         }
         public function isSeq()
         {
            return $this->isSeq;
         }
         public function setIsSeq($isSeq)
         {
            $this->isSeq = $isSeq;
         }

         public function setFullScreenMode($fullscreenmode)
         {
            $this->fullScreenMode = $fullscreenmode;
         }

         public function isFullScreenMode()
         {
            return $this->fullScreenMode;
         }
         
         public function getPasswordFieldMapping($dbname)
         {
             $result = null;
             if(is_array($this->passwordFieldMapping[$dbname])&&count($this->passwordFieldMapping[$dbname])>0)
             {
                $result = $this->passwordFieldMapping[$dbname];
             }
             return $result;
         }
         public function setPasswordMinLength($minLength)
         {
            $this->passwordMinLength = $minlength;
         }
         public function setMethodSuccessString($methodSuccessString)
         {
            $this->methodSuccessString = $methodSuccessString;
         }  

         public function getMethodSuccessString()
         {
            return $this->methodSuccessString;
         }

         public function setMethodFailString($methodFailString)
         {
            $this->methodFailString = $methodFailString;
         }  

         public function getMethodFailString()
         {
            return $this->methodFailString;
         }  


         public function setTableWidth($tableWidth)
         {
            $this->tableWidth = $tableWidth;
         }

         public function setAllowWrap($allowWrap)
         {
             $this->allowWrap = $allowWrap;
         }

         public function getTableWidth()
         {
            return $this->tableWidth;
         }
         public function isAllowWrap()
         {
            return $this->allowWrap ;
         }

         public function getTmpPath()
         {
            $path = FileTools::connectPath(QuickFormConfig::$tmpPath,"export".date("YmdHis").StringTools::getRandStr());
            $path = FileTools::getRealPath($path);
            if(!is_dir($path))
            {
                mkdir($path,777);
            }
            return $path;
         }
         public function exportFiles($path)
         {      
            $result = $this->getResult();
            for($i=0;$i<count($result);$i++)
            {
                foreach($this->exportFileField as $k=>$e)
                {
                    $isRealFilePath = $e["isRealFilePath"];
                    $exportFileMethod = $e["exportFileMethod"];
                    $file = $this->$exportFileMethod($i,$k,false);
                    $name = $this->$exportFileMethod($i,$k,true);             
                    if($isRealFilePath)
                    {
                        if($file!=null&&trim($file)!=""&&is_file(trim($file)))
                        {
                            FileTools::copyFile($file,FileTools::connectPath($path,$name),true);
                        }
                    }
                    else
                    {
                        if($file!=null&&trim($file)!="")
                        {
                            FileTools::downloadFile($file,$path,$name,false);
                        }
                    }
                }
            }
         }

         public function exportCsvZip($fileName,$data=null,$withTitle=true,$src=null,$method=null)
         {
            $path = $this->getTmpPath();
            $this->exportFiles($path);
            $fullpath  = FileTools::connectPath($path,$fileName);
            $this->writeFile($fullpath,$data,$withTitle);
            $zipName = $this->getExportFileName("zip",false);
           
            $this->exportZip($path,$zipName);
         }
         public function exportPdfZip($fileName,$exportObj=null,$withTitle=true,$src=null,$method=null)
         {
            $this->exportFileZip($fileName,$exportObj,$withTitle,$src,$method,"PDF");
         }
         public function exportXlsxZip($fileName,$exportObj=null,$withTitle=true,$src=null,$method=null)
         {
            $this->exportFileZip($fileName,$exportObj,$withTitle,$src,$method,"Excel2007");
         }
         public function exportXlsZip($fileName,$exportObj=null,$withTitle=true,$src=null,$method=null)
         {
            $this->exportFileZip($fileName,$exportObj,$withTitle,$src,$method,"Excel5");
         }
   

         protected function exportFileZip($fileName,$exportObj=null,$withTitle=true,$src=null,$method=null,$format) 
         {
              $path = $this->getTmpPath();
               $this->exportFiles($path);
              $fullpath  = FileTools::connectPath($path,$fileName);
              if($exportObj==null)
              {
                $exportObj = $this->createExportObj($withTitle);
              } 
              $objWriter = PHPExcel_IOFactory::createWriter($exportObj, $format);  
              $objWriter->save($fullpath);   
              $zipName = $this->getExportFileName("zip",false);
              $this->exportZip($path,$zipName);    
        }

         protected function exportZip($path,$fileName)
         {
            $zipFile = FileTools::getRealPath(FileTools::connectPath(QuickFormConfig::$tmpPath,"export".date("YmdHis").StringTools::getRandStr().".zip"));
            
            FileTools::zip($zipFile,$path,$path,true);
            header("content-type:application/zip");  
            header('Content-Disposition: attachment; filename='.$fileName);     
            header('Content-Transfer-Encoding: binary');     
            header('Expires: 0');     
            header('Cache-Control: must-revalidate, post-check=0, pre-check=0');     
            header('Pragma: public');     
            header('Content-Length:'. filesize($zipFile));   
            ob_end_clean();   
            flush();     
            readfile($zipFile);   
            unlink($zipFile); 
            FileTools::unlinkDir($path);
            die();
         }







         public function setMultiLineSetting($dbname,$size=10,$cols=100)
         {
            $this->multiLineSetting[$dbname]= Array("size"=>$size,"cols"=>$cols);
         }
         public function getMultiLineSetting($dbname)
         {
            $result =  Array("size"=>10,"cols"=>100);
            if(isset($this->multiLineSetting[$dbname])&&is_array($this->multiLineSetting[$dbname]))
            {
                $result =  $this->multiLineSetting[$dbname];
            }
            return $result;
         }

        public function initCustomProcessMethod($src)
        {
            
        }
        public function setCustomProcessMethod($processName,$methodName,$buttonText,$editButtonText=null,$initEditMethod="initEdit",$addMode=false)
        {
            if($editButtonText==null||trim($editButtonText)=="")
            {
                $editButtonText = $buttonText;
            }
            $this->customProcessMethod[$processName] = Array("methodname"=>$methodName,"buttontext"=>$buttonText,"editbuttontext"=>$editButtonText,"initeditmethod"=>$initEditMethod,"addMode"=>$addMode);
            $this->setMethod($processName,$methodName,true);
        }
        public function getEditProcessButtonHtml($processName)
        {
            $customProcessMethod = $this->getCustomProcessMethod($processName);
            $text = $customProcessMethod["editbuttontext"];
            $id = "process_".$processName;
            $html = new HtmlElement();
            $js = "_submit('".$processName."')"; 
            $html->setFunction("onclick",$js);
            return  $html->getSubmit($text);
        }
         public function getCustomOperationButtons()
         {
            return $this->customOperationButtons;
         }
         public function setCustomOperationButton($buttonid,$text,$onClick=null,$paramsArray=Array())
         {
           
            $id = "cob_".$buttonid;
            $html = new HtmlElement();
            if($onClick!=null&&trim($onClick)!="")
            {
                $paramsArray["onClick"] = $onClick;
            }
            foreach($paramsArray as $i => $v)
            {
                $html->setParam($i,$v);
            }
            $this->customOperationButtons[$buttonid] =  $html->getButton($text);
            
         }
         public function getProcessButtonHtml($processName)
        {

            $customProcessMethod = $this->getCustomProcessMethod($processName);
            $text = $customProcessMethod["buttontext"];
            $addMode = $customProcessMethod["addMode"];
            $id = "process_".$processName;
            $html = new HtmlElement();
            $js = "_customProcess('".$processName."')"; 
            if($addMode)
            {
               $js = "_editItem('','".$processName."')";
            }
            $html->setFunction("onclick",$js);
            return $html->getButton($text);
        }
        public function getCustomProcessMethod($processName=null)
        {
           
           $result = $this->customProcessMethod;

           if($processName!=null&&trim($processName)!="")
           {
                $result = $this->customProcessMethod[$processName];
           }
           return $result;
        }

        public function setFinalWhereClause($whereClause)
        {
            $this->finalWhereClause = $whereClause;
        }
        public function getFinalWhereClause()
        {
            return $this->finalWhereClause;
        }
        public function setMainIdCol($mainIdCol,$oriDbName=null)
        {    
            $this->mainIdCol = Array("dbname"=>$mainIdCol,"oridbname"=>$oriDbName,"isMethod"=>false,"methodName"=>null);
        }
        public function setMainIdMethod($mainIdCol,$methodName)
        {
               $this->mainIdCol = Array("dbname"=>$mainIdCol,"oridbname"=>null,"isMethod"=>true,"methodName"=>$methodName);
        }
        public function getMainIdDbName()
        {
            return $this->mainIdCol["dbname"];
        }
        public function getMainIdOriDbName()
        {
            $dbname = $this->getMainIdDbName();
            $colInfo = $this->getColInfo();
            return ArrayTools::getValueFromArray($colInfo,$dbname);
        }
        public function getMainIdCol()
        {
            return $this->mainIdCol;
        }

        public function getData($db,$sql,$orderBy,$pagerows=0,$curpage=1,$sqlBuilder=null)
        { 

           $this->setDb($db);
           $fullSql = $sql." ".$orderBy;
           $this->setExecSql($fullSql);
           $this->getSqlBuilder($sqlBuilder);
           $dataMsg = new DataMsg($db);
           $srvSqlMainCol  = null;
           if($db->isMssql())
           {
                $srvSqlMainCol = $dataMsg->getSrvSqlMainCol();
                $dataMsg->setSrvSqlMainCol($this->mainIdCol["dbname"]); 
           }
          
           $dataMsg->findByPageSql($db,$sql,$pagerows,$curpage,$this->getCountCol(),"",$sqlBuilder,false,$orderBy);
           if($db->isMssql())
           {
                $dataMsg->setSrvSqlMainCol($srvSqlMainCol); 
           }
           $this->totalcount = $dataMsg->getTotalCount();

           $this->pagerows = $dataMsg->getPageRows();
           $this->curpage = $dataMsg->getCurPage();
           $this->totalpages = $dataMsg->getTotalPages();
           $this->result = Array();
           for($i=0;$i<$dataMsg->getSize();$i++)
           {
               $data = $dataMsg->getData($i);
               $r =  $data->getDataArray();
               $this->result[] =$this->modifyData($r);
           }
       }
        
        public function modifyData($data)
        {
            return $data;
        }

        public function setLinkTable($dbname,$linkkey,$datasign,$tablename,$pk)
        {

            $this->linkInfo[trim($datasign)] = Array(
                                                    "linkkey" => trim($linkkey),
                                                    "linkdbname" => trim($dbname),
                                                    "tablename" => trim($tablename),
                                                    "pk" => trim($pk),
                                                    "linktype" => "table",
                                                    "datasign" => trim($datasign),
                                                );
        }
       
        public function getDefaultExportFormat()
        {
            return $this->defaultExportFormat;
        }
        public function haveOtherExportFormat()
        {
            $result = false;
            foreach($this->exportFormat as $format => $bool)
            {
                if($bool)
                {
                    $result = true;
                    break;
                }
            }
            return $result;
        }
        public function isSupportExportFormat($format)
        {
            $result = false;
            $format = strtoupper(trim($format));
            if(is_bool($this->exportFormat[$format])&&$this->exportFormat[$format])
            {
               $exportFormatMethod = $this->exportFormatMethod;
               if(count($this->exportFileField)>0)
               {
                   $exportFormatMethod = $this->exportZipFormatMethod;
               }
               if($exportFormatMethod[$format]!=null&&$exportFormatMethod[$format]!="")
               {
                   $result = true;
               }
            }
            return $result;
        }
        public function setDefaultExportFormat($format)
        {
            $format = strtoupper(trim($format));
            if($this->isSupportExportFormat($format))
            {
                foreach($this->exportFormat as $aformat => $bool)
                {
                    if($aformat == $format)
                    {
                        $bool  = false;
                    }
                    $this->exportFormat[$aformat] = $bool;
                }

                $this->defaultExportFormat = $format;
            }
        }
        public function getExportFormatMethod($format)
        {
            $result = $this->exportFormatMethod[strtoupper(trim($format))];
            if(count($this->exportFileField)>0)
            {
                $result = $this->exportZipFormatMethod[strtoupper(trim($format))];
            }
            return $result;
        }
        public function setExportFormatMethod($format,$method)
        {
           $this->exportFormatMethod[strtoupper(trim($format))] = $method;
        }
        public function setExportFormat($format,$bool)
        {
            $this->exportFormat[$format] = $bool;
        }
        public function getExportFormat()
        {

            return  $this->exportFormat;
        }
        public function getCustomSearchMethod()
        {
            return $this->customSearchMethod;
        }
        public function setCustomSearchMethod($key,$method,$skipWhere=false,$skipHaving=false)
        {
            $this->customSearchMethod[$key] = Array("method"=>$method,"skipWhere"=>$skipWhere,"skiphaving"=>$skipHaving);
        }
      
        public function setSqlGroup($dbname,$id,$sql)
        {
            $this->sqlGroup[$dbname][$id] = $sql;
        }

        public function setSearchFieldMapping($dbname,$dstdbname)
        {
            $this->searchFieldMapping[$dbname] = $dstdbname;

        }
        public function getSearchFieldMapping($dbname)
        { 
            return ArrayTools::getValueFromArray($this->searchFieldMapping,$dbname);
        }
        public function setSearchGroup($groupid,$dbname,$text="",$relation="AND")
        {
            $this->searchGroup[$groupid][$dbname] = Array("relation"=>$relation,"text"=>$text);
            $this->searchGroupSetting[$groupid]["relation"] = "AND"; 
        }
        public function removeSearchGroup($groupid,$dbname)
        {   
        
            unset($this->searchGroup[$groupid][$dbname]);
            if(count($this->searchGroup[$groupid])==0)
            {
                unset($this->searchGroup[$groupid]);
            }

        }   
        public function setSearchGroupText($groupid,$dbname,$text)
        {
             $this->searchGroup[$groupid][$dbname]["text"] = $text;
        }

        public function setSearchGroupRelation($groupid,$relation)
        {
              $this->searchGroupSetting[$groupid]["relation"] = $relation; 
        }

        public function setSearchGroupName($groupid,$groupname)
        {
            $this->searchGroupSetting[$groupid]["groupname"] = $groupname;
            $this->searchGroupSetting[$groupid]["relation"] = "AND"; 
        }

        public function getSearchGroupName($groupid)
        { 
            $ret = null;
            if(isset($this->searchGroupSetting[$groupid]["groupname"]))
            {
               $ret = $this->searchGroupSetting[$groupid]["groupname"];
            }
            return  $ret;
        }
        public function getSearchGroupRelation($groupid)
        {
            $ret = null;
            if(isset($this->searchGroupSetting[$groupid]["relation"]))
            {
               $ret = $this->searchGroupSetting[$groupid]["relation"];
            }
            return  $ret;
        }
        public function getSearchGroup()
        {
            return $this->searchGroup;
        }

        public function getDisplayName($dbname,$must=false,$editform=false)
        {
            $filed = $this->getFieldByName($dbname);
            $name =  $filed["displayname"];
            if($must&&($name==null||trim($name)==""))
            {
                $name = $dbname; 
            }
            if($name==null||trim($name)=="")
            {
                 $name = " ";
            }
            $validateRules = $this->getValidateRules();
            if($editform&&isset($validateRules["rules"][$this->getEditPrefix().$dbname]["required"])&&$validateRules["rules"][$this->getEditPrefix().$dbname]["required"])
            {
                $name .= " (*)";
            }
            return $name;
        }
        


         public function setVoidBootStrap($dbname,$bool)
         {
            $this->voidBootStrap[$dbname] = $bool;
        }
         public function setSearchBarCollapse($searchBarCollapse)
         {
            $this->searchBarCollapse = $searchBarCollapse;
         }
         public function getSearchBarCollapse()
         {
            return $this->searchBarCollapse;
         }
         public function setHaving($having)
         {
            $this->havingString = $having;
         }
         

         public function getHaving()
         {
            return  $this->havingString;
         }
         
         public function setEditColMapping($dbname,$mappingMethod)
         {
                $this->editColMapping[$dbname] = $mappingMethod;
         }
         public function getEditColMappingJs($formId)
         {
            $js = ""; 
            foreach($this->editColMapping as $dbname =>$mappingMethod)
            {
               if(isset($this->editCustomField[$dbname]))
               {
                    $sign = $dbname;
               }
               else
               {
                    $sign = $this->getEditPrefix().$dbname;
               }
               $js     .= "$('#".$sign."').blur(function(){"
                       .  "_editColMapping('".$formId."','".$sign."'".$mappingMethod."');"
                       ."});";
            }
            return "<script>".$js."</script>";
         }
         public function setSearchMapping($dbname,$key)
         {
            $this->searchMapping[$dbname] = $key;
         }
         public function getSearchMapping($dbname=null)
         {
            $result =  $this->searchMapping;
            if($dbname!=null)
            {
                $result = ArrayTools::getValueFromArray($result,$dbname);
            }
            return $result;
         }

         public function setExportMethod($exportMode,$method)
         {
            $exportMode = $this->getExportMode($exportMode);
            $this->exportMethod[trim(strtolower($exportMode))] = trim($method);
    
         }
         public function getExportMode($exportMode)
         {
            $exportMode = trim(strtolower($exportMode));
            $result = $exportMode;
            if($exportMode!="all"&&$exportMode!="page")
            {
                if(StringTools::isStartWith($exportMode,"all"))
                {
                    $result = substr($exportMode,3);
                }
            }
            return $result;
         }
         public function getExportFormatString($format)
         {
            if($format==null||!$this->isSupportExportFormat($format))
            {
                $format = $this->defaultExportFormat;
            }
            return $format;
         }
         public function getExportMethod($exportMode,$format=null)
         {
            $exportMode = $this->getExportMode($exportMode);
            $format = $this->getExportFormatString($format);
            $result = $this->getExportFormatMethod($format);
            if($exportMode!="all"&&$exportMode!="page")
            {
                if($this->exportMethod[$exportMode]!=null&&trim($this->exportMethod[$exportMode])!="")
                {    
                    $result = trim($this->exportMethod[$exportMode]);
                }
            }
            return $result;
         }
         public function setWhereGroup($dbname,$id,$name,$where,$formname=null)
         {
            $this->whereGroup[$dbname][$id] = Array("id"=>$id,"name"=>$name,"where"=>$where,"formname"=>$formname); 
         }
         public function setFormNameByGroup($src,$dbname,$defaultValue)
         {

             $sign = $this->getSearchPrefix().$dbname;
             $id = $defaultValue;
             if($src[$sign]!=null&&trim($src[$sign])!="")
             {
                $id = $src[$sign];
             }  
             if($id!=null&&trim($id)!=""&&is_array($this->whereGroup[$dbname][$id]))
             {
                $formname = $this->whereGroup[$dbname][$id]["formname"];
                if($formname!=null&&trim($formname)!="")
                {
                      $this->setReportName($formname);
                }
             }
    
         }
         public function isAddMode()
         {
            return $this->isAddMode;
         }
         public function setIsAddMode($isAddMode)
         {
            return $this->isAddMode = $isAddMode;
         }
        public function setDeteleValidateMethod($deteleValidateMethod)
        {
            $this->deteleValidateMethod = $deteleValidateMethod;
        }
        public function getDeteleValidateMethod()
        {
            return $this->deteleValidateMethod;
        }
        public function setEditFormId($editFormId)
        {
            $this->editFormId = $editFormId;
        }
        public function getEditFormId()
        {
            return $this->editFormId;
        }
        public function setIsReport($isReport)
        {
            $this->isReport = $isReport;
        }

        public function isReport()
        {
            return  $this->isReport;
        }

        public function setFormMark($formMark)
        {

             $this->formMark = $formMark;

        }

        public function getFormMark()
        {
            if(!$this->isReport)
            {
                 $this->formMark = get_class($this);
            }
            return  $this->formMark;
        }
         public function addEditFieldLink($dbname,$dstdbname)
         {
            $this->editFieldLink[$dbname][] = $dstdbname;
         }
         public function clearEditFieldLink($dbname) 
         {
             $this->editFieldLink[$dbname][] = Array();
         }
        public function setCheckBoxesSetting($dbname,$withAll=true,$spiltBy=",")
        {
            $this->checkBoxesSetting[$dbname]= Array("withAll"=>$withAll,"spiltBy"=>$spiltBy);
        }
        public function setExportTemplate($dbname,$templateKey)
        {
            $this->exportTemplate[$dbname] = $templateKey;  
        }
        public function getExportTemplate($dbname)
        {
            return ArrayTools::getValueFromArray($this->exportTemplate,$dbname);
        }
          public function setSearchTemplate($dbname,$templateKey)
        {
            $this->searchTemplate[$dbname] = $templateKey;  
        }
        public function getSearchTemplate($dbname)
        {
            return ArrayTools::getValueFromArray($this->searchTemplate,$dbname);
        }
        public function setReportTemplate($dbname,$templateKey)
        {
            $this->reportTemplate[$dbname] = $templateKey;  
        }
        public function getReportTemplate($dbname)
        {
           return ArrayTools::getValueFromArray( $this->reportTemplate,$dbname);
        }
        public function setEditTemplate($dbname,$templateKey)
        {
            $this->editTemplate[$dbname] = $templateKey;  
        }
        public function getEditTemplate($dbname)
        {
         return ArrayTools::getValueFromArray( $this->editTemplate,$dbname);
        }
        public function getValueMark()
        {
            
            return $this->valueMark;
        }
        public function setTemplate($key,$template)
        {
            $this->template[$key] = $template;           
        }
        public function getTemplate($key)
        {
         return ArrayTools::getValueFromArray( $this->template,$key);
            
        }

         public function setQuickEditPrefix($quickEditPrefix)
         {
            $this->quickEditPrefix = $quickEditPrefix;
         }
         public function getQuickEditPrefix()
         {
            return $this->quickEditPrefix;
         }
         public function setInitMethod($initMethod)
         {
            $this->initMethod = $initMethod;
         }
         public function getInitMethod($src)
         {
            return $this->initMethod;
         }
         public function initCustomMethod($src=null)
         {

         }
          public function setInitEditMethod($initEditMethod)
         {
            $this->initEditMethod = $initEditMethod;
         }
         public function getInitEditMethod($src)
         {
            return $this->initEditMethod;
         }

        public function setEditInputButton($dbname,$echo,$value)
        {
            if($value==null) 
            {
                 unset($this->editInputButton[$dbname][$echo]);
            }
            else
            {
                $this->editInputButton[$dbname][$echo] = StringTools::escapeJsString($value);
            }
        }

        protected function addQuickAjaxVaildateRule($dbname,$value)
        {
                $url = QuickFormConfig::$quickFormMethodPath."quickAjaxVaildate.php";
                $data = Array();

                $tmpdata = Array();
                foreach($this->editField as $tmpdbname=>$data)
                {   
                  
                    $tmp = $this->getEditPrefix().$tmpdbname;
                    $tmpdata["quickAjax_".$tmp] = "function(){return $(\"#".$tmp."\").val();}";
            
                }
                foreach($this->editCustomField as $id=>$data)
                {
                    $tmpdata["quickAjax_".$id] .= "function(){return $(\"#".$id."\").val();}";
                }
                $value = trim( $value,",");
                $array = Array();
                $array["url"] = $url;
                $array["type"] = 'POST';
                $array["dataType"] = 'json';
                
                $data = array_merge($data,$tmpdata);
               
                $data["quickAjaxMethod"] = $value;
                $data["formMark"] = $this->getFormMark();
                $data["isreport"] = "0";
                $data["dbname"] = $dbname;
                if($this->isReport())
                {
                     $data["isreport"] = "1";
                }
                if($_REQUEST["ed_dataid"]!=null&&trim($_REQUEST["ed_dataid"])!="")
                {
                        $data["mainid"] =  $_REQUEST["ed_dataid"];
                }
                $array["data"] =  $data;
            
            $this->validateRulesMapping["rules"][$this->getEditPrefix().$dbname]["quickAjax"]=$array;
        }
        public function getVaildateRulesMessage($dbname,$rule)
        {
            return $this->validateRulesMapping["messages"][$this->getEditPrefix().$dbname][$rule];
        }

        public function setVaildateRulesMessage($dbname,$rule,$message)
        {
            $this->validateRulesMapping["messages"][$this->getEditPrefix().$dbname][$rule]=$message;
        }
        public function addValidateRule($dbname,$rule,$value,$message=null)
        {
            if($rule=="quickAjax")
            {
                $this->addQuickAjaxVaildateRule($dbname,$value);
                //$rule = "remote";
            }
            else
            {
                $this->validateRulesMapping["rules"][$this->getEditPrefix().$dbname][$rule]=$value;
            }
            if($message!=null&&trim($message)!="")
            {
                $this->validateRulesMapping["messages"][$this->getEditPrefix().$dbname][$rule]=$message;
            }
        }

        public function getValidateRules()
        { 
            $mapping = $this->validateRulesMapping;
            $minLength = $this->passwordMinLength;
            foreach($this->passwordFieldMapping as $dbname => $passwordMapping)
            {
                $mapping["rules"][$passwordMapping["password"]]["required"] = true;
                 $mapping["rules"][$passwordMapping["password"]]["minlength"] = 1;
                if($passwordMapping["passwordagain"]!=null&&trim($passwordMapping["passwordagain"])!="")
                {
                    $mapping["rules"][$passwordMapping["passwordagain"]]["required"] = true;
                    $mapping["rules"][$passwordMapping["passwordagain"]]["minlength"] = 1;
                    $mapping["rules"][$passwordMapping["passwordagain"]]["equalTo"] = "#".$passwordMapping[$password].$this->getEditPrefix().$dbname;
                      $mapping["messages"][$passwordMapping["passwordagain"]]["equalTo"] = "These passwords don\'t match";
                }
                if($minLength!=null&&trim($minLength)!="")
                {
                    $mapping["rules"][$passwordMapping["password"].$this->getEditPrefix().$dbname]["minlength"] = $minLength;
                     if($passwordMapping["passwordagain"]!=null&&trim($passwordMapping["passwordagain"])!="")
                     {
                        $mapping["rules"][$passwordMapping["passwordagain"]]["minlength"] = $minLength;
                     }
                }
            }
            return $mapping;
        }

        public function loadValidateJs($editor=false)
        {
            $result = "";
            $rules = $this->getValidateRules();
            if(count($rules)>0)
            {
                if($editor)
                {
                      $result = '<script type="text/javascript" src="'.QuickFormConfig::$jqueryValidationPath.'jquery.validate.quickform.js"></script><script type="text/javascript" src="'.QuickFormConfig::$jqueryValidationPath.'additional-methods.min.js"></script>';
                }
                else
                {
                     $result = '<script type="text/javascript" src="'.QuickFormConfig::$jqueryValidationPath.'jquery.validate.min.js"></script><script type="text/javascript" src="'.QuickFormConfig::$jqueryValidationPath.'additional-methods.min.js"></script>';
                }
            }
           if(is_array($this->checkRule)&&count($this->checkRule)>0)
           {
                $result = '<link href="'.QuickFormConfig::$qnuiPath.'css/sui.min.css" rel="stylesheet"><script type="text/javascript" src='.QuickFormConfig::$qnuiPath.'js/sui.min.js"></script>';
          
           }
           return $result;
        }

        public function getValidateScript($formid=null,$withScript=true)
        {
            if($formid==null||trim($formid)!="")
            {
                $formid = $this->getEditFormId();
            }
            $rules = $this->getValidateRules();
            
            $result = "";
            if(count($rules)>0)
            {   
                $result = "";
                if($withScript)
                {
                    $result = "<script>";
                }
                $result .= '$( "#'.$formid.'" ).validate({';
                $result .='errorPlacement : function(error, element) {
if (element.is(":text"))
{
    if(element.val()!="")
    {
        error.insertAfter( element );
    }
    else
    {
        element.attr("placeholder",error.text());
    }
}
else
{
error.insertAfter( element );
}
},';
                $result .= jqueryTools::arrayToString($rules);
                $result .= "});";
                if($withScript)
                {
                    $result .="</script>";
                }
            }
            return $result; 
        }
        public function prepare($src=null)
        {
            
        }
        public function setCustomInitMapping($dbname,$value,$initMethod="init",$initEditMethod="initEdit",$initLayoutMethod="initLayout")
        {
            $this->customInitMapping[$dbname][$value] = Array(
                                                        "init"=>$initMethod,
                                                        "initEdit"=>$initEditMethod,
                                                        "initLayout"=>$initLayoutMethod,
                                                     );
        }
        public function getCustomInitMapping($src)
        {
             $result = null;
             foreach($this->customInitMapping as $dbname=>$initSetting)
             {

                $sign = $this->getSearchPrefix().$dbname;
    
                $value = $src[$sign];

                if($value!=null&&$value!="")
                {
                    $tmp = $this->customInitMapping[$dbname][$value];
                    if($tmp!=null&&is_array($tmp))
                    {
                        $result = $tmp;
                        break;
                    }
                } 
             }
             return $result;
             
        }
        public function setFindInSetMode($dbname,$mode)
        {
            $this->fileInSetModeArray[$dbname] = $mode;
        }
        public function getFindInSetMode($dbname)
        {
            $tmp = $this->fileInSetModeArray[$dbname];
            $result = false;
            if($tmp!=null&&is_bool($tmp))
            {
                $result = $tmp;
            }
            return $result;
        }
        public function getUploaderMapping()
        {
            return $this->uploaderMapping;
        }
        public function getUploader($dbname)
        {

            return $this->uploaderMapping[$dbname];
        }
        public function setUploader($dbname,$uploader,$path,$extension,$process=null,$save=true,$savepath="",$override=false,$spiltBy=";")
        {
            return $this->uploaderMapping[$dbname] = Array("uploader"=>$uploader,"path"=>$path,"process"=>$process,"save"=>$save,"extension"=>$extension,"override"=>$override,"spiltBy"=>$spiltBy,"savepath"=>$savepath);
        }
         public function addTranColMapping($srcdbname,$dstdbname,$sql,$tranColName=null)
         {
            $array = Array("dstdbname"=>$dstdbname,"sql"=>$sql,"trancolname"=>$tranColName,"ids"=>"");
            $this->tranColMapping[$srcdbname] = $array;
         }

         public function editSrc($db,$src)
         {
            return $src;
         }

         public function fileSearchShowMode($dbname,$colname,$src,$sql=false,$defaultValue="")
         {
               $sign = $this->getSearchPrefix().$dbname;  
               if($sql)
               {
                  $sql = " ".$colname." like '".$src[$sign]."' ";
                  return $sql;
               }
             
               $html = new HtmlElement($sign,$sign);
               $result = null;
               if($this->isEditMode())
               {
                  $result = $html->getFile();
               }
               else
               {
                  $result = $html->getInput($defaultValue);
               }
               return $result;

         }

         public function getIdStrFromArray($key,$array=null)
         {  
            if($array==null)
            {
                $array = $this->getResult();
            }
            $result = "";
            for($i=0;$i<count($array);$i++)
            {
                $tmp = $array[$key];
                if($tmp!=null&&trim($tmp)!="")
                if(trim($result==""))
                {
                    $result .= "'".$tmp."'";
                }
                else
                {
                    $result .= ",'".$tmp."'";
                }   
            }
            return $result;
         }

         public function proessingColMapping($db)
         {
            $tranColMapping = $this->tranColMapping;
            if(is_array($tranColMapping)&&count($tranColMapping)>0)
            {
                $result = $this->getResult();
                for($i=0;$i<$result->getSize();$i++)
                {
                    foreach($tranColMapping as $srcdbname=>$colArray)
                    {
                        $value = $result[$i][$srcdbname];
                        $ids = $colArray["ids"];
                        if($value!=null&&trim($value)!="")
                        {
                            if(trim($ids)!="")
                            {
                                $ids .= ",";
                            }
                            $ids .="'".$value."'";
                        }
                    }
                    $colArray["ids"] = $ids;
                    $tranColMappingp[$srcdbname] = $colArray;
                }
                $valueMapping = Array();
                foreach($tranColMapping as $srcdbname=>$colArray)
                {
                    $ids =  $colArray["ids"];
                    if($ids!=null&&trim($ids)!="")
                    {
                        $sql =  $colArray["sql"];
                        $sql.=" WHERE ".$srcdbname." IN (".$ids.")";
                    }
                    $datamsg = new DataMsg();
                    $datamsg->findBySql($db,$sql);
                    $dstdbname = $colArray["dstdbname"];
                    $temp = $datamsg->getKeyValueArray($srcdbname,$dstdbname);
                    $valueMapping[$srcdbname] =  $temp;
                }
                $newResult = Array();
                for($i=0;$i=$this->getResult();$i++)
                {
                    $tmp = $result[$i];
                    foreach($tranColMapping as $srcdbname=>$colArray)
                    {
                        $dstdbname = $colArray["dstdbname"];
                        $src = $tmp[$srcdbname];
                        $tranColName = $colArray["trancolname"];
                        if($trancolname!=null&&$trancolname!="")
                        {
                            $dstdbname = $trancolname;
                        }
                        $value = $valueMapping[$srcdbname][$src];
                        $tmp[$dstdbname] = $value;
                    }
                    $newResult[] = $tmp;
                }
                $this->setResult($newResult);
            } 
            

         }



         public function setTipCol($name,$dbname,$method=false,$methodName=null)
         {
            $this->tipCol[$name] = Array("dbname"=>$dbname,"method"=>$method,"methodName"=>$methodName);
     
         }
         public function addSpanAttribute($row,$dbname,$key,$value)
         {
            $name = $dbname;
            $this->spanAttributes[$row][$name][$key] = $value;


         }

         public function addSpanTip($row,$dbname,$value)
         {
            $this->addSpanAttribute($row,$dbname,"title",$value);
         }

         protected function getSpanHtml($row,$name,$html)
         {
            $atts = Array();
            if(isset($this->spanAttributes[$row][$name]))
            {
               $atts =  $this->spanAttributes[$row][$name];
            }
            if(!(isset($atts["title"])&&$atts["title"]!=null&&trim($atts["title"])!=""))
            {
                $tipCol = ArrayTools::getValueFromArray($this->tipCol,$name);
                
                if(is_array($tipCol))
                {
                    $tCol = $tipCol["dbname"];
                    $tips = $this->getValueByDbName($row,$tCol,false);  
                    if($tipCol["method"])
                    {
                        $methodName = $tipCol["methodName"];
                        if($methodName!=null&&trim($methodName)!="")
                        {
                          
                            $tips = $this->$methodName($row,$name,true);             
                        }
                        else
                        {
                            $tips = null;
                        }
                    }
                   
                    $this-> addSpanTip($row,$name,$tips);
                }
            }


            $atts = Array();
            if(isset($this->spanAttributes[$row][$name])) 
            {      
                  $atts =  $this->spanAttributes[$row][$name];
            }     
            if(is_array($atts)&&count($atts)>0)
            {
                $spanHtml = "<span width='100%'";
                foreach($atts as $key=>$value)
                {
                    $spanHtml .=" ".$key." = '".$value."' ";  
                }
                $spanHtml.=">".$html."</span>";
                $html = $spanHtml;
            }
            return $html;
         }

         protected function getParentDbNameForLinkDbName($dbName)
         {
               $result = null;
               $dbNameInfo = $this->fields[$dbName];
               $isLinkField = $dbNameInfo["islinkfield"];
               if($isLinkField)
               {
                    $dataSign = $dbNameInfo["dataSign"];
                    $linkInfo = $this->linkInfo[$dataSign];
                    $result = $linkInfo["linkdbname"];
               }
               return $result;
         }

         public function getAllLinkData($dataArray=null)
         {
            $this->linkData = Array();
            foreach($this->linkInfo as $dataSign => $linkInfo)
            {
                if(isset($this->linkPath[$dataSign])&&!isset($this->linkData[$dataSign]))
                {
                    
                    $db = $this->getDb();
                    $data = null;
                    $tablename = $linkInfo["tablename"];
                    $pk = $linkInfo["pk"];
                    $data = new Data($db,$tablename,$pk);
                    $data->setTableSign($dataSign);

                    $srcdbname = $this->linkPath[$dataSign]["srcdbname"];
                    $srcdbnameinfo = $this->fields[$srcdbname];
                    $ismultivalueinrow =  $srcdbnameinfo["ismultivalueinrow"];
                    if($dataArray==null)
                    {
                        $dataArray = $this->getResult();
                    }
                    $whereValue = "";
                    $unique = array();

                    foreach($dataArray as $row)
                    {
                        $value = $row[$srcdbname];
                        if($ismultivalueinrow)
                        {
                             $spiltby = $srcdbnameinfo["spiltby"];
                             $valueArray = explode($spiltby,$value);
                             foreach($valueArray as $v)
                             {
                                if($v!=null&&trim($v)!=""&&!in_array($v, $unique))
                                {
                                    $unique[] = $v;
                                    $whereValue .= ",'".$v."'";
                                }
                             }
                        }
                        else
                        {
                                if($value!=null&&trim($value)!=""&&!in_array($value,$unique))
                                {
                                    $unique[] = $value;
                                    $whereValue .= ",'".$value."'";
                                }
                        }
                    }
                    $whereValue = ltrim($whereValue,",");
                    $pointCol = null;
                    if(trim($whereValue)!="")
                    {
                        if(isset($this->linkPath[$dataSign]["path"]))
                        {
                            $path = $this->linkPath[$dataSign]["path"];
                         
                            for($i=0;$i<count($path);$i++)
                            {
                                $i_sign = $path[$i];

                                $i_linkkey = $this->linkInfo[$i_sign]["linkkey"];
                                $i_dbname = $this->linkInfo[$i_sign]["linkdbname"];
                                $i_field = $this->fields[$i_dbname];
                                if($i_field["islinkfield"])
                                {
                                    $src_linkdatasign = $i_field["linkdatasign"];
                                    $src_linkkey = $i_field["linkkey"];
                                    $datasign =  $i_field["datasign"];
                                    $i_onClause = $datasign.".".$i_linkkey." = ".$src_linkdatasign.".".$src_linkkey;
                                    $data->setSubTable($i_sign,$this->linkInfo[$i_sign]["tablename"],$this->linkInfo[$i_sign]["pk"],$i_onClause,"RIGHT JOIN");
                                }
                                else
                                {

                                    $pointCol = $linkInfo["linkkey"];
                                    if($dataSign != $i_sign)
                                    {
                                        $pointCol =$i_sign."_".$pointCol;
                                    }
                                    $data->setWithOperator($linkInfo["linkkey"],"(".$whereValue.")","IN");    
                                }
                            }
                        }
                        else
                        {
                            $pointCol = $linkInfo["linkkey"];
                             $data->setWithOperator($linkInfo["linkkey"],"(".$whereValue.")","IN");  
                        }
                        $dataMsg = $data->find();

                        $this->linkData[$dataSign]["value"] = $dataMsg->getKeyDataMap($pointCol,true,true);
                        $this->linkData[$dataSign]["id"] = $dataMsg->getKeyValueMap($pointCol,$linkInfo["pk"],true);
                      
                    }
                }
            }
        
         }

         public function getAllLinkPath()
         {
            foreach($this->linkInfo as $dataSign => $linkInfo)
            {

                if(!isset($this->linkPath[$dataSign]["srcdbname"]))
                {
                    $this->getPathForLinkDataSign($dataSign);
                }
            }
         }

        

         protected function getPathForLinkDataSign($dataSign)
         {
           
            $srcdbname = null;
            if(is_array($this->linkInfo[$dataSign]))
            {
               
                $linkdbname = $this->linkInfo[$dataSign]["linkdbname"];
                $path = $this->getPathForLinkDbName($linkdbname);
                if($path==null)
                {
                    $this->linkPath[$dataSign]["srcdbname"] = $linkdbname;

                }
                else
                {
                    $path[]  = $dataSign;
                    $lastdatasign = $this->result[count($this->result)-1];
                    $srcdbname = $this->linkInfo[$lastdatasign]["linkdbname"];
                    for($i=1;$i<=count($path);$i++)
                    {
                       
                        $tmp = array_slice($path,0, $i);
                        $lastdatasign = $path[$i-1];
                        $this->linkPath[$lastdatasign]["srcdbname"] = $srcdbname;
                        $this->linkPath[$lastdatasign]["path"] = array_reverse($tmp);
                    }
                }
            }


         }

         protected function getPathForLinkDbName($dbName,$reverse=true)
         {
             
             $result = null;
             $dbNameInfo =  $this->fields[$dbName];
            
             $isLinkField = $dbNameInfo["islinkfield"];
             $tmpDbName = $dbName;
             if($isLinkField)
             {
                $result = Array();
                $tmp = Array();
                while(1==1)
                {
                    $currentDbName = $tmpDbName;
                    $tmpDbName = $this->getParentDbNameForLinkDbName($tmpDbName);
                    if($tmpDbName==null)
                    {
                        $result["srcdbname"] = $currentDbName;
                        break;
                    } 
                    else
                    {
                          $dbNameInfo =  $this->fields[$currentDbName];
                          $dataSign = $dbNameInfo["linkdatasign"];
                          $tmp[] = $dataSign;
                    }
                }
                if($reverse)
                {
                    $tmp = array_reverse($tmp);
                }
                $result["datasign"] = $tmp;

             }


             return $result;

         }
      
         public function getOriValueByDbName($row,$dbName,$forceOri=false)
         {
              $field = ArrayTools::getValueFromArray($this->fields,$dbName);
              $isLinkField = false;
              if(is_array($field)&&isset($field["islinkfield"]))
              {
                  $isLinkField = $field["islinkfield"];
               }
              $result = "";
              if($isLinkField)
              {
                 $dataSign = $field["linkdatasign"];
                 $linkkey = $field["linkkey"];
                 $srcdbname = $this->linkPath[$dataSign]["srcdbname"];
                 
                 $tmp = strval(parent::getOriValueByDbName($row,$srcdbname,$forceOri));
            
                 $srcField = $this->fields[$srcdbname];
                 $dataArray = Array();
                 if($srcField["ismultivalueinrow"])
                 {
                    $tmpArray = explode($srcField["spiltby"],$tmp);
                    foreach($tmpArray as $t)
                    {
                        $dataArray = array_merge($dataArray,$this->linkData[$dataSign]["value"][$t]);
                    }
                 }
                 else
                 {
                    $dataArray = $this->linkData[$dataSign]["value"][$tmp];
                  
                 }
                 foreach($dataArray as $d)
                 {
                    $result .=$field["spiltby"].$d[$linkkey];
                 }
                 $result = ltrim($result,$field["spiltby"]);
              }
              else
              {
                 $result = parent::getOriValueByDbName($row,$dbName,$forceOri);
              }
              return $result;
         }

         public function getValueByMethod($row,$dbName,$isExport=false,$method=null,$forceOri=false)
         {
            $result = null;
            if($method!=null&&trim($method)!="")
            {
                   
                $result = $this->getValueByDbName($row,$dbName,true,$isExport,true,$method,$forceOri);
                
            }
            else
            {
            
                $result = $this->getOriValueByDbName($row,$dbName,$forceOri);
                

            }
            return $result;
         }

         public function getValueByDbName($row,$dbName,$isTableItem=true,$isExport=false,$customMethod=false,$customMethodName=null,$forceOri=false)
         {    
            
                  $html = parent::getValueByDbName($row,$dbName,$isTableItem,$isExport,$customMethod,$customMethodName,$forceOri);
                  if(!$isExport&&!$forceOri&&$isTableItem)
                  {
                    $html = $this->getSpanHtml($row,$dbName,$html);
                     if($this->hasExtendInfo($dbName)&&!$this->isExportMode())
                     {
                        $html.=" ".'<i  style="cursor:pointer" class="quickform_extendinfobutton far fa-plus-square"></i>';
                     }
                  }
                  if($isTableItem)
                  {
                      $template = null;
                      if($isExport)
                      {
                        $template = $this->getExportTemplate($dbName);
                      }
                      else
                      {
                        $template = $this->getReportTemplate($dbName);
                      }

                      if($template!=null&&trim($template)!="")
                      {
                         $templateHtml = $this->getTemplate($template);
                         if($templateHtml!=null&&trim($templateHtml)!="")
                         {
                       
                            $html = str_replace($this->getValueMark(),$html,$templateHtml);
                         }
                      }
                  }
               
              return $html;
         }
         public function getMethodSuccess($method)
         {
            return $this->methodSuccess[$method];
         }
         public function setMethodSuccess($method,$msg,$url=null)
         {
                $this->methodSuccess[$method] = Array("msg"=>$msg,"url"=>$url);

         }
         public function getMethodWarning($method)
         {
            return $this->methodWarning[$method];
         }
         public function setMethodWarning($method,$msg)
         { 
                $this->methodWarning[$method] = $msg;
         }
         public function showEditCustomShowModeById($id,$isAjax=false,$value=null,$methodname=null)
         {
            return $this->showEditCustomShowMode($this->editCustomField[$id],$isAjax,$value,$methodname);
         }

         public function showEditCustomShowMode($customArray,$isAjax=false,$value=null,$methodname=null)
         {   
              $id = $customArray["id"];
              if($methodname==null||trim($methodname)=="")
              {
                $methodname = $customArray["method"];
              }
              if($value==null)
              {
                $value = $customArray["value"];
              }
              $searchPrefix = $this->getSearchPrefix();
              $prefix = "";
              $this->setSearchPrefix($prefix);
              $html = $this->$methodname($id,$id,null,false,$value);
              $this->applyEditFieldLink($id);
               $this->applyColMapping($id,"edit");
              $html = $this->addAttrJs($id, $html,"edit");
              $this->setSearchPrefix($searchPrefix);
              if(!$isAjax)
              {
                $html = $this->getEditDivHtml($id,$html);
              }
              return $html;           
         }

        public function addEditCustomHtml($id,$name,$html)
        {
            $this->editCustomHtml[$id]  = Array("name"=>$name,"html"=>$html);
        }

        public function getEditCustomHtmls()
        {
            return $this->editCustomHtml;
        }

        public function addEditCustomField($id,$name,$method,$value=null)
        {
             $array = Array(
                        "id"=>$id,
                        "name"=>$name,
                        "method"=>$method,
                        "value"=>$value,
                        );
             $this->editCustomField[$id] = $array;
        }

        public function getEditCustomField()
        {
            return $this->editCustomField;
        } 
         public function addAttachDataByData($db,$key,$data,$idKey="id",$nameKey="name")
         {
            $data->setDb($db);
            $datamsg =  $data->find();
            $array = $datamsg->getDataArray();
            return $this->addAttachData($key,$array,$idKey,$nameKey);
         }
         public function addAttachDataBySql($db,$key,$sql,$idKey="id",$nameKey="name")
         {
            $datamsg = new DataMsg();
            $datamsg->findBySql($db,$sql);
            $array = $datamsg->getDataArray();
            return $this->addAttachData($key,$array,$idKey,$nameKey);
         }
         public function addAttachDataByMap($key,$array,$switch=false)
         {
            $finalArray =  Array();
            foreach($array as $id => $name)
            {
                if($switch)
                {
                    $tmp = $id;
                    $id = $name;
                    $name = $tmp;
                }
                $d = Array();
                $d["attachdata_id"] = $id;
                $d["attachdata_name"] = $name;
                $finalArray[] = $d;
            }    
            $this->attachData[$key] = $finalArray;
         }
         public function addAttachData($key,$array,$idKey="id",$nameKey="name")
         {
            
                $finalArray =  Array();
                foreach($array as $d)
                {
                    $id = $d[$idKey];
                    $name = $d[$nameKey];
                    $d["attachdata_id"] = $id;
                    $d["attachdata_name"] = $name;
                    $finalArray[] = $d;
                }
           
            $this->attachData[$key] = $finalArray;
         }
         public function clearAttachData()
         {
            $this->attachData = Array();
         }


         public function getAttachData($key=null,$getKvArray=false,$strKey=false)
         {
            $result = $this->attachData;

            if($key!=null&&trim($key)!="")
            {
                $result = $this->attachData[$key];
            }
            if($getKvArray)
            {
               $tmp = Array();
               foreach($result as $r)
               {
                  $k =  $r["attachdata_id"];
                   $v =  $r["attachdata_name"];
                   if($strKey)
                   {
                     $k = strval($k);
                   }
                   $tmp[$k] = $v;
               }
               $result = $tmp;
            }
            return $result;
         }
         public function addAttachDataByArray($key,$array)
         {
             $this->attachData[$key] = $array;
         }

         public function setAttachDataByMap($keystr,$map,$swap=false)
         {
            $newArray = Array();
            foreach($map as $key =>$value)
            {
                $arr = Array();
                $k = $key;
                $v = $value;
                if($swap)
                {
                    $k = $value;
                    $v = $key;
                }
                $arr["attachdata_id"] = $k;
                $arr["attachdata_name"] = $v;
                $newArray[] = $arr;
            }

            $this->attachData[$keystr] = $newArray;
         }


         public function setAttachData($key,$array,$idKey="id",$nameKey="name")
         {
           $newArray = Array();
           foreach($array as $arr)
           {
               if(isset($arr[$idKey]))
               {
                 $arr["attachdata_id"] = $arr[$idKey];
               }
               if(isset($arr[$nameKey]))
               {
                 $arr["attachdata_name"] = $arr[$nameKey];
               } 
               $newArray[] = $arr;
           }
           $this->attachData[$key] = $newArray;
         }

         public function __call($name,$arguments)
         {
            $result = null;
            foreach($this->magicFunction as $magicFunction => $commonFunction)
            {
                if(StringTools::isStartWith($name,$magicFunction))
                {
                    $result  = $this->$commonFunction($name,$arguments);
                    break;
                }
            }

            return $result;

            


         }



        public function getCommonAjaxDataBy($name,$arguments)
        {
             $method = substr($name,strlen("getAjaxDataBy"));
             $row = $arguments[0];
             $dbname = $arguments[1];
             $mainIdDbName = $this->getMainIdDbName();
             $id = $this->getValueByDbName($row,$mainIdDbName,false);
             $html = "<div id='ajaxData_".$dbname."_".$id."'></div>";
             $url =QuickFormConfig::$quickFormMethodPath."getAjaxFieldData.php?method=".$method."&formmark=".$this->getFormMark()."&isreport=".$this->isReport()."&dbname=".$dbname."&id=".$id;
             $html.='<script>
              $.get("'.$url.'", function(data){
              
                $("#ajaxData_'.$dbname.'_'.$id.'").html(data);
                    });
             </script>';
             return $html;
        }
        
        public function getCommonRadiosBy($name,$arguments)
        {
             $leftStr = substr($name,strlen("getRadiosBy")); 
             $keyStr = $leftStr;
             $dbname = $arguments[0];
             $colname =  $arguments[1];
             $src =  $arguments[2];
             $sql = $arguments[3];
             $defaultValue = $arguments[4];
             $sign = $dbname;
             if(!$this->isEditMode())
             {
                $sign = $this->getSearchPrefix().$dbname; 
             } 
             if($sql)
             {                  
                $sql = " ".$colname." = '".$src[$sign]."' ";
                if($this->getFindInSetMode($dbname))
                {
                    if($this->getDb()->isMssql())
                    {
                         $sql = ' \',\'+'.$colname.' +\',\' like \'%,'.$src[$sign].',%\'';
                    }
                    else
                    {

                        $sql = " FIND_IN_SET('".$src[$sign]."', ". $colname.") ";
                    }

                }
                
                return $sql;
             }
            $html = new HtmlElement($sign,$sign);
            $list = $this->getAttachData($keyStr);
            return $html->getRadiosWithHiddenByList($list,"attachdata_id","attachdata_name",$defaultValue);
                 
        }

        public function getCommonCheckboxesBy($name,$arguments)
        {


                $leftStr = substr($name,strlen("getCheckboxesBy"));     
                $keyStr = $leftStr;
                $dbname = $arguments[0];
                $colname =  $arguments[1];
                $src =  $arguments[2];
                $sql = $arguments[3];
                $defaultValue = $arguments[4];
                $sign = $dbname;

                if(!$this->isEditMode())
                {
                    $sign = $this->getSearchPrefix().$dbname; 
                }

                $htmlid = $this->getSearchPrefix().$dbname; 
                $plist =null;
                if(isset($src[$sign])&&$src[$sign]!=null&&trim($src[$sign]!= ''))
                {
                 
                  $psqllist = '';
                  $plist = '';
                  $i=0;
                  $arr = $src [$sign];
                  if(!is_array($arr))
                  {
                    $arr = explode(",", $arr);
           
                  }

                  $tmpStr = "";
                  foreach ( $arr as $value ) {
                         
                 
                    if($i==0)
                    {
                      $plist .= $value;
                      $psqllist .="'".$value."'";
                    } else
                    {
                      $plist .= ','.$value;
                      $psqllist .=",'".$value."'";
                    }

                    if(trim($tmpStr)=="")
                    {
                        if($this->getDb()->isMssql())
                        {
                            $tmpStr .= ' \',\'+'.$colname.' +\',\' like \'%,'.$value.',%\'';
                        }
                        else
                        {
                             $tmpStr .= " FIND_IN_SET('".$value."', ". $colname.") ";
                        }
                    }
                    else
                    {
                         if($this->getDb()->isMssql())
                        {
                             $tmpStr .= ' AND \',\'+'.$colname.' +\',\' like \'%,'.$value.',%\'';
                        }
                        else
                        {
                             $tmpStr .= " AND FIND_IN_SET('".$value."', ". $colname.") ";
                        }    
                    }
                    $i++;
                  }
                }      
      
                 if ($sql) 
                 {

                    if($this->getFindInSetMode($dbname))
                    {
                         $sql = $tmpStr;
                    }
                    else
                    {
                        $colDetail = $this->colDetail[$dbname];
                        $type = strtolower(trim($colDetail["type"]));
                        if($type==null||trim($type)=="")
                        {
                             $type = strtolower(trim($colDetail["type_name"]));
                        }
                        if(!StringTools::isStartWith($type,"bigint")
                        &&!StringTools::isStartWith($type,"int")
                        &&!StringTools::isStartWith($type,"mediumint")
                        &&!StringTools::isStartWith($type,"smallint")
                        &&!StringTools::isStartWith($type,"tinyint")
                        &&!StringTools::isStartWith($type,"decimal")
                        &&!StringTools::isStartWith($type,"double")
                        &&!StringTools::isStartWith($type,"float")
                        &&!StringTools::isStartWith($type,"numeric")
                        &&!StringTools::isStartWith($type,"money")
                        &&!StringTools::isStartWith($type,"smallmoney")
                        )
                      {
                         $tmp = explode(",", $plist);
                         
                         $plist  = "";
                         foreach($tmp as $t)
                         {
                            $plist.= ",'".$t."'";
                         }
                        
                         $plist = ltrim($plist,",");

                      }
                         $sql = " " . $colname . " IN (" . $plist . ") ";
                    }
                  
                    return $sql;
                 }
                          
                if(isset($src[$sign])&&($src[$sign]==null||trim($src[$sign]== '')))
                {
                    $plist = $defaultValue;
                }
                $list = $this->getAttachData($keyStr);
                $result = null;
                $html = new HtmlElement ( $htmlid . "[]", $htmlid . "[]" );
                $tmp = Array();
                foreach($list as $l)
                {
                  $tmp[$l["attachdata_id"]] = $l["attachdata_name"];
                }
                $withAll = true;
                $spiltBy =",";
                $checkBoxesSetting = ArrayTools::getValueFromArray($this->checkBoxesSetting,$dbname);
                if(is_array($checkBoxesSetting))
                {
                    $withAll = $checkBoxesSetting["withAll"];
                    $spiltBy = $checkBoxesSetting["spiltBy"];
                }
               
                $temp = $html->getCheckBoxesByArray($tmp, $plist,true,$spiltBy,$withAll);
                $result = "";
                $i = 0 ;
                if($withAll)
                {
                    $i = 3;
                }
                foreach ( $temp as $checkbox ) {

                  $i+=strlen($checkbox ["echo"]) + 3;
                  if($i>=120)
                  {
                    $result .= "<br>";
                    $i = 0;
                  }
                  $result .= $checkbox ["checkBox"] . " " . $checkbox ["echo"]. " ";
                
                }
                return $result;
        }
        public function getCommonTextAreaWithClass($name,$arguments)
        {

            $class = substr($name,strlen("getTextAreaWithClass"));
            $dbname = $arguments[0];
            $colname = $arguments[1];
            $src = $arguments[2];
            $sql = $arguments[3];
            $defaultValue = "";
            if($arguments[4]!=null&&trim($arguments[4])!="")
            {
                $defaultValue = $arguments[4];
            }
            return  $this->textAreaSearchShowMode($dbname,$colname,$src,$sql,$defaultValue,false,20,100,$class);
        }

        public function translateByAttachData($arrayName,$srcValue)
        {
            $array = $this->getAttachData($arrayName);
            $array = ArrayTools::getKeyValueArray($array,"attachdata_id","attachdata_name");
   
            return $array[$srcValue];
        }
        public function getCommonTranslateBy($name,$arguments) 
        {
             $arrayName = substr($name,strlen("translateBy"));
             $row = $arguments[0];
             $dbname = $arguments[1];
             $export = $arguments[2];
             $array = $this->getAttachData($arrayName);
             $array = ArrayTools::getKeyValueArray($array,"attachdata_id","attachdata_name");
             return $this->commonTranslateShowMode($row, $dbname,$export,$array);
        }
        protected function getTreeArray($treeObject,$translateData,$parentKey=null,$selected="")
        {
           $result = Array();
           $cdata = Array();
           $selectedArray = explode(",",$selected); 
           if($parentKey!=null&&trim($parentKey)!="")
           {
                $cdata = $treeObject->getChild($parentKey,false,false,false);
           }
           else
           {
                $cdata = $treeObject->getTopNodeIdArray();
        
           }   

           foreach($cdata as $nodeId)
           {
               
                $nodeTitle = $translateData[$nodeId];
                $nodeArray = Array();
                $nodeArray["title"] = $nodeTitle;
                $nodeArray["key"] = $nodeId;
                $nodeArray["expanded"] =false;
                if(in_array(trim(strval($nodeId),"'"), $selectedArray))
                {
                  $nodeArray["selected"] =true;
                }
                if($treeObject->hasChild($nodeId))
                {

                   $nodeArray["children"] = $this->getTreeArray($treeObject,$translateData,$nodeId,$selected);
                }
                $result[] = $nodeArray;
           }
           return $result;

        }
        public function getQuickFormCommonExtendInfo($src)
        {
            $dbname = $src["dbname"];
            $method = "";
            if($this->extendInfoMethod[$dbname]!=null&&trim($this->extendInfoMethod[$dbname]!=""))
            {
               $method =$this->extendInfoMethod[$dbname];
            }
            else if($this->defaultInfoMethod!=null&&trim($this->defaultInfoMethod)!="")
            {
                $method =$this->defaultInfoMethod;
            }
            $mainid= $src["mainid"];
            $dataArray = $this->getFormDataByMainId($this->db, $mainid);
            echo $this->$method($src,$dataArray);
        }
        public function getQuickFormCommonTreeData($src)
        {
          $treekey = $src["treekey"];
          $selected = $src["selected"];
          $treeObject = $this->treeData[$treekey];
          $translateData = $this->getAttachData($treekey);
          $translateData = ArrayTools::getKeyValueArray($translateData,"attachdata_id","attachdata_name");
          $treeArray = $this->getTreeArray($treeObject,$translateData,null,$selected);
          echo json_encode($treeArray);
        }
        public function getQuickFormCommonAjaxTreeData($src)
        {
          $treekey = $src["treekey"];
          $selected = $src["selected"];
          $id = $src["ajaxTreeNodeKey"];
          $dataMethod = $this->ajaxTreeSetting[$treekey]["dataMethod"];
          $idKey = $this->ajaxTreeSetting[$treekey]["idKey"];
          $nameKey = $this->ajaxTreeSetting[$treekey]["nameKey"];
          $translateBy = $this->ajaxTreeSetting[$treekey]["translateBy"];
          $tmp = $this->$dataMethod($id,$src,$selected); 
          $selectedArray = explode(",",$selected); 
          $treeArray = Array();
          foreach($tmp as $t)
          {
                $nodeArray = Array();
                $nodeKey = $t[$idKey];
                $nodeArray["key"] = $nodeKey;
                $nodeTitle =$t[$nameKey];
                if($translateBy!=null&&trim($translateBy)!="")
                {
                   $srcValue = $nodeTitle;
                   if($srcValue==null||trim($srcValue)=="")
                   {
                     $srcValue = $nodeKey;
                   }
                   $nodeTitle = $this->translateByAttachData($translateBy,$srcValue);
                }
                $nodeArray["title"] = $nodeTitle;
                $nodeArray["expanded"] =false;
                $nodeArray["lazy"] = "true";
                if(in_array(trim(strval($nodeKey),"'"), $selectedArray))
                {
                  $nodeArray["selected"] =true;
                }
                $treeArray[] = $nodeArray;
          }
          echo json_encode($treeArray);
        }
        public function getCommonTreeBy($name,$arguments)
        {
                $leftStr = substr($name,strlen("getTreeBy"));     
                $keyStr = $leftStr;
                $dbname = $arguments[0];
                $colname =  $arguments[1];
                $src =  $arguments[2];
                $sql = $arguments[3];
                $defaultValue = $arguments[4];
                $sign = $this->getSearchPrefix().$dbname; 
                $treesign = "div_tree_".$sign;
             $value = $src[$dbname];
             
             if($sql)
             {
                
                 $sql = " " . $colname . " IN (" . $value . ") ";
                return $sql;
             }
             else
             {
                $isReport = 0;
                if($this->isReport())
                {
                  $isReport = 1;
                }
               /* $treeObject = $this->treeData[$keyStr];
                $translateData = $this->getAttachData($keyStr);
                $translateData = ArrayTools::getKeyValueArray($translateData,"attachdata_id","attachdata_name");
                $treeArray = $this->getTreeArray($treeObject,$translateData);*/
                $html.="<input id='".$sign."' name='".$sign."' value=\"".$value."\" type='hidden' />";
                $html.= "<div style='height:100%' id='".$treesign."'></div>";
                $html.='<script type="text/javascript">';
                $html.='  $(function(){
                          $("#'.$treesign.'").fancytree({
                          checkbox: true,
                          selectMode: 3,
                        source:  {
                                url: "'.QuickFormConfig::$quickFormMethodPath.'getAjaxHtml.php",
                                type: "POST",
                                data:{formmark:"'.$this->getFormMark().'",isreport:'.$isReport.',method:"getQuickFormCommonTreeData",treekey:"'.$keyStr.'",selected:"'.str_replace("'","",$value).'"}
                              },
                        
                           
                          init: function(event, data) {
                            data.tree.visit(function(n) {
                            });
                          },
                            select: function(event, data) {
                            // Display list of selected nodes
                            var s = data.tree.getSelectedNodes();
                            var ret ="";
                            s.forEach(function(node) {
                                if(ret!="")
                                {
                                    ret = ret + ",";
                                }
                                ret = ret+"\'"+ node.key+ "\'";
                             });
                            $("#'.$sign.'").val(ret);
                          }
                        });

                        });
                    ';
                $html.='</script>';
                return $html;
             }
        }

         public function getCommonAjaxTreeBy($name,$arguments)
        {
                $leftStr = substr($name,strlen("getAjaxTreeBy"));     
                $keyStr = $leftStr;
                $dbname = $arguments[0];
                $colname =  $arguments[1];
                $src =  $arguments[2];
                $sql = $arguments[3];
                $defaultValue = $arguments[4];
                $sign = $this->getSearchPrefix().$dbname; 
                $treesign = "div_tree_".$sign;
                $value = $src[$sign];
                
                $sqlMethod = $this->ajaxTreeSetting[$keyStr]["sqlMethod"];
             if($sql)
             {
                $sql = " " . $colname . " IN (" . $value . ") ";
                if($sqlMethod!=null&&trim($sqlMethod)!="")
                {
                   $sql = $this->$sqlMethod($dbname,$colname,$src,$value,$defaultValue);
                }
                return $sql;
             }
             else
             {
                $isReport = 0;
                if($this->isReport())
                {
                  $isReport = 1;
                }
               /* $treeObject = $this->treeData[$keyStr];
                $translateData = $this->getAttachData($keyStr);
                $translateData = ArrayTools::getKeyValueArray($translateData,"attachdata_id","attachdata_name");
                $treeArray = $this->getTreeArray($treeObject,$translateData);*/
                $html.="<input id='".$sign."' name='".$sign."' value=\"".$value."\" type='hidden' />";
                $html.= "<div style='height:100%' id='".$treesign."'></div>";
                $html.='<script type="text/javascript">';
                $html.='  $(function(){
                          $("#'.$treesign.'").fancytree({
                          checkbox: true,
                          selectMode: 3,
                        source:  {
                                url: "'.QuickFormConfig::$quickFormMethodPath.'getAjaxHtml.php",
                                type: "POST",
                                data:{formmark:"'.$this->getFormMark().'",isreport:'.$isReport.',method:"getQuickFormCommonAjaxTreeData",treekey:"'.$keyStr.'",selected:"'.str_replace("'","",$value).'"}
                              },
                        lazyLoad: function(event, data) {
                              var node = data.node; 
                             data.result = {
                               url: "'.QuickFormConfig::$quickFormMethodPath.'getAjaxHtml.php",
                                type: "POST",
                                data:{formmark:"'.$this->getFormMark().'",isreport:'.$isReport.',method:"getQuickFormCommonAjaxTreeData",treekey:"'.$keyStr.'",selected:"'.str_replace("'","",$value).'",ajaxTreeNodeKey: node.key}
                           };},   
                          init: function(event, data) {
                            data.tree.visit(function(n) {
                            });
                          },
                            select: function(event, data) {
                            // Display list of selected nodes
                            var s = data.tree.getSelectedNodes();
                            var ret ="";
                            s.forEach(function(node) {
                                if(ret!="")
                                {
                                    ret = ret + ",";
                                }
                                ret = ret+"\'"+ node.key+ "\'";
                             });
                            $("#'.$sign.'").val(ret);
                          }
                        });

                        });
                    ';
                $html.='</script>';
                return $html;
             }
        }

        public function commonTranslateShowMode($row, $dbname, $export=false,$array=null)
        {
             $value = $this->getValueByDbName($row,$dbname,false);
             
             if($array!=null)
             {
                $value = $array[$value];
             }
             return $value;
        }
        
        public function getCommonImagePickerBy($name,$arguments)
        {
                $leftStr = substr($name,strlen("getImagePickerBy"));              
                $keyStr = $leftStr;
                $dbname = $arguments[0];
                $colname =  $arguments[1];
                $src =  $arguments[2];
                $sql = $arguments[3];
                $defaultValue = $arguments[4];
                $sign = $this->getSearchPrefix().$dbname;  
                if($sql)
                {
                  $sql = " ".$colname." = '".$src[$sign]."' ";
                    if($this->getFindInSetMode($dbname))
                    {
                        if($this->getDb()->isMssql())
                        {
                              $sql = ' \',\'+'.$colname.' +\',\' like \'%,'.$src[$sign].',%\'';
                        }
                        else
                        {
                            $sql = " FIND_IN_SET('".$src[$sign]."', ". $colname.") ";
                        }
                    }
                  return $sql;
                }
                $list = $this->getAttachData($keyStr);
                $html = new HtmlElement($sign,$sign);
                $result = null;
                $result = "<select id='".$sign."' name='".$sign."' class='image-picker'>"; 
                foreach($list as $d)
                {
                    $result .= "<option value='".$d["attachdata_id"]."'  data-img-src='". $d["attachdata_name"]."' >".($i+1)."</option>";
                }
                $result.="</select>";
                $result.="<script>$('#".$sign."').imagepicker()</script>";
                return $result;
        }

      
         public function getCommonMultiSelectBy($name,$arguments)
        {

            return $this->getCommonSelectBy($name,$arguments,"getMultiSelectBy",true);
        }
        
        public function getCommonMultiTagBy($name,$arguments)
        {
            return $this->getCommonSelectBy($name,$arguments,"getMultiTagBy",true,true);
        }
        public function getCommonMultiAjaxTagBy($name,$arguments)
        {
            return $this->getCommonSelectBy($name,$arguments,"getMultiAjaxTagBy",true,true,true);
        }
        public function getCommonAjaxTagBy($name,$arguments)
        {
            return $this->getCommonSelectBy($name,$arguments,"getAjaxTagBy",false,true);
        }
        public function getCommonTagBy($name,$arguments)
        {
            return $this->getCommonSelectBy($name,$arguments,"getTagBy",false,true);
        }
        public function getCommonMultiAjaxSelectBy($name,$arguments)
        {
          return $this->getCommonSelectBy($name,$arguments,"getMultiAjaxSelectBy",true,false,true);
        }
        public function getCommonAjaxSelectBy($name,$arguments)
        {
        	 return $this->getCommonSelectBy($name,$arguments,"getAjaxSelectBy",false,false,true);
        }
        public function getCommonAutoCompleteBy($name,$arguments)
        {
            $leftStr = null;
            $leftString="getAutoCompleteBy";
            $leftStr = substr($name,strlen($leftString));
            $dbname = $arguments[0];
            $colname =  $arguments[1];
            $src =  $arguments[2];
            $sql = $arguments[3];
            $defaultValue = $arguments[4];
            $sign = $this->getSearchPrefix().$dbname;    
            $html = new HtmlElement($sign,$sign);
            $html->setParam("class","autocomplete_".$dbname." form-control");
            $js= '
            $(".autocomplete_'.$dbname.'").on("keyup", function(e) {
   
                var code = (e.keyCode || e.which);      
                 if (code === 37 || code === 38 || code === 39 || code === 40 || code === 27 || code === 13) {    
                   //reserved 
                               
                 } else {
                    var awesomplete = new Awesomplete(this);
                     var ajax = new XMLHttpRequest();
                     ajax.onreadystatechange = function() {                    
                         if (this.readyState == 4 && this.status == 200) {         
                             var data = JSON.parse(this.responseText);            
                             var list = [];                                                                                 
                             for (var i=0; i<data.length; i++) {
                               list.push(data[i].id,data[i].text)
                             }                        
                            
                             awesomplete.list = list
                             
                         } 
                     }

                    ajax.open("GET", "'.QuickFormConfig::$quickFormBasePath."quickform/quickAjaxSelectData.php?formmark=".$this->getFormMark()."&isreport=".$this->isReport()."&dbname=".$dbname."&method=".$leftStr.$this->getAjaxCustomDataStr($dbname,false).'&keyword="+this.value, true);
                     ajax.send();
                     this.focus(); 
                 }
              });
               ';   
            $this->setCustomJs("initCol_".$dbname,$js);
              if($sql)
            {
                       $sql = " ".$colname." = '".$src[$sign]."' ";
                        if($this->getFindInSetMode($dbname))
                        {
                            if($this->getDb()->isMssql())
                            {
                                 $sql = ' \',\'+'.$colname.' +\',\' like \'%,'.$src[$sign].',%\'';
                            }
                            else
                            {
                                $sql = " FIND_IN_SET('".$src[$sign]."', ". $colname.") ";
                            }
                        }
                        return $sql;
            }
            return $html->getInput($defaultValue);   
        }

        public function getCommonSmartSelectBy($name,$arguments)
        {
            $leftString = "getSmartSelectBy"; 
            return $this->getCommonSoundWarningSelectBy($name,$arguments,$leftString,false);
        }
        public function getCommonSoundWarningSelectBy($name,$arguments,$leftString="getSoundWarningSelectBy",$isSoundWarning=true)
        {
            $playRing = "";
            $playError = "";
            if($isSoundWarning)
            {
               $playRing = " ion.sound.play('bell_ring');";
               $playError = "ion.sound.play('computer_error');";
            }
            $dbname = $arguments[0];
            $ajaxjs = "if(data.length==0)
            {"
              .$playError. 
              "\$(':focus').val('');
            }
            else if(data.length==1)
            {
               var option = new Option(data[0].text, data[0].id, true, true);
               var obj = $('[aria-expanded=\"true\"]').parent().parent().parent().children().first();
               obj.append(option).trigger('change');
               obj.select2('close');
               "
              .$playRing. 
              " 
            }
            return {results: data};";
            $js ="$(document).ready(function() {ion.sound({
                      sounds: [
                          {name: 'bell_ring'},
                          {name: 'computer_error'}
                      ],
                      path: '".QuickFormConfig::$quickFormResourcePath."ion.sound/sounds/',
                      preload: true,
                      multiplay: true,
                      
                  });});";
              $this->setCustomJs("quickform_soundWarning",$js);
            return $this->getCommonSelectBy($name,$arguments,$leftString,false,false,true,$ajaxjs);
        }
     public function getCommonSelectBy($name, $arguments, $leftString = "getSelectBy", $multi = false, $tag = false, $ajax = false, $ajaxjs = null, $attachjs = "") {

      $leftStr = null;
      $leftStr = substr($name, strlen($leftString));
      $tagStr = "";

      if ($tag) {
         $tagStr = ",tags: true";
      }
      $withStr = null;
      $tmp = strstr($leftStr, "With");
      $keyStr = $leftStr;
      if ($tmp) {
         $withStr = substr($tmp, strlen("With"));
         $keyStr = substr($leftStr, 0, strlen($leftStr) - strlen($tmp));
      }
      $dbname = $arguments[0];

      $colname = $arguments[1];
      $src = $arguments[2];
      $sql = $arguments[3];
      $defaultValue = $arguments[4];
      $sign = $this->getSearchPrefix() . $dbname;
      $signSelect = $sign;
      if ($multi) {
         $signSelect .= "_select";
      }

      $list = Array();
      $ajaxStr = "";
      if (!$ajax) {
         $list = $this->getAttachData($keyStr);
      } else {
         if ($ajaxjs == null || trim($ajaxjs) == "") {
            $ajaxjs = "return {results: data};";
         }
         $ajaxStr = ",ajax: {
                         url: function () {
                           return '" . QuickFormConfig::$quickFormBasePath . "/quickform/quickAjaxSelectData.php?formmark=" . $this->getFormMark() . "&isreport=" . $this->isReport() . "&dbname=" . $dbname . $this->getAjaxCustomDataStr($dbname) . "&method=" . $keyStr . "';
                         },

                         dataType: 'json',
                         delay: 250,
                         data: function (params) {
                           return {
                             keyword: params.term, // search term
                             page: params.page
                           };
                         },
                         processResults: function (data, params) {
                          " . $ajaxjs . "
                         },

                       },
                       escapeMarkup: function (markup) { return markup; },
                       minimumInputLength: 1,
                       templateResult: formatTemplate_".$dbname.",
                       templateSelection: formatSelection_".$dbname.",
                       ";
      }
      $html = new HtmlElement($signSelect, $signSelect);

      $result = null;
      /*if($this->isEditMode())
                        {
                           $result = $html->getSelectByArray($list,"id","name",$defaultValue);
                        }
                        else
      */

      $selectOption = $this->getCommonSelectOption($dbname);
      $className =  $dbname."_".StringTools::getRandStr();
      //$className =  $dbname;
      $html->setParam("class",  $className);
      $customOnChangeStr = "";
      if (isset($this->commonSelectOption[$dbname]["onChange"]) && $this->commonSelectOption[$dbname]["onChange"] != null && trim($this->commonSelectOption[$dbname]["onChange"]) != "") {
         $customOnChangeStr = trim($this->commonSelectOption[$dbname]["onChange"]);
      }
      $onChange = $customOnChangeStr;
      if ($multi) {
         $onChange = "$('#" . $sign . "').val($('#" . $signSelect . "').select2('val'));$('#" . $sign . "').change();" . $customOnChangeStr;
      }
      foreach ($selectOption as $key => $value) {
         if (trim(strtolower($key)) == "onchange") {
            $onChange .= $value;
         } else {
            $html->setParam($key, $value);
         }
      }
      $html->setFunction("onChange", $onChange);
      $clearStr = "";
      if ($multi) {
         $html->setParam("multiple", "multiple");
         $valueArray = explode(",", $defaultValue);
         $result = $html->getSelectByArray($list, "attachdata_id", "attachdata_name", $valueArray, "", null, false);
      } else {
         $result = $html->getSelectByArray($list, "attachdata_id", "attachdata_name", $defaultValue, $withStr);
         if ($withStr == null) {
            $clearStr = "placeholder:'',allowClear: true,";
         }
      }

      //  $result .='<script>$("#'.$sign.'").chosen({disable_search_threshold: 6,width: "100%"});</script>';
      //}

      $js = "$(document).ready(function() {\$('." .  $className . "').select2({" . $clearStr . "width: '100%'" . $tagStr . $ajaxStr . "});";
      if ($multi) {
         $hidden = new HtmlElement($sign, $sign);
         $result .= $hidden->getHidden($src[$sign]);
         $js = "$(document).ready(function() {\$('#" . $sign . "').val(\$('#" . $signSelect . "').val()); \$('." . $className  . "').select2({width: '100%'" . $tagStr . $ajaxStr . "});";
      }
      if ($ajax) {
         $selectedvalue = $defaultValue;
         if(isset($src[$sign])&&$src[$sign]!=null&&trim($src[$sign])!=null)
         {
            $selectedvalue =$src[$sign];
         }
         
         $tmpArr = explode(",", $selectedvalue);
         if ($selectedvalue != null && trim($selectedvalue) != "" && count($tmpArr) > 0) {
            $valArr = $this->$keyStr($this->getDb(), $dbname, $selectedvalue, true);
            foreach ($tmpArr as $t) {
               $tv = "";
               $tn = "";
               $match = false;
               foreach ($valArr as $v) {

                  if (strval($t) == strval($v["id"])) {
                     $tv = $t;
                     $tn = $v["text"];
                     if ($v["selection"] != null && trim($v["selection"]) != "") {
                        $tn = $v["selection"];
                     }
                     $match = true;
                  }
               }
               if (!$match) {
                  $tv = $t;
                  $tn = $t;
               }

               $js .= "\$('." .  $className . "').append(new Option('" . $tn . "','" . $tv . "', false, true));";
            }
         }

      }
      $formatTemplate = " function formatTemplate_".$dbname."(repo) {
                     if (repo.loading) return repo.text;
                         var markup = repo.text;
                         return markup;
                     }";
        if (isset($this->commonSelectFunction[$dbname]["formatTemplate"]) && $this->commonSelectFunction[$dbname]["formatTemplate"] != null && trim($this->commonSelectFunction[$dbname]["formatTemplate"]) != "") {
           $formatTemplate = $this->commonSelectFunction[$dbname]["formatTemplate"];
        }
      $formatSelection = " function formatSelection_".$dbname."(repo) {
                        if (repo.loading) return repo.text;
                         var markup = repo.selection;
                         if(!markup)
                         {
                            markup = repo.text;
                         }
                         return markup;
                     }";

         if (isset($this->commonSelectFunction[$dbname]["formatSelection"]) && $this->commonSelectFunction[$dbname]["formatSelection"] != null && trim($this->commonSelectFunction[$dbname]["formatSelection"]) != "") {
           $formatSelection = $this->commonSelectFunction[$dbname]["formatSelection"];
        }
      $js .= "});".$formatTemplate.$formatSelection;
      $js .= $attachjs;

      if ($sql) {
         if ($multi) {
            $sql = " ";
            $valueArray = explode(",", $src[$sign]);
            foreach ($valueArray as $value) {
               if ($this->getFindInSetMode($dbname)) {
                  if ($this->getDb()->isMssql()) {
                     $sql .= ' \',\'+' . $colname . ' +\',\' like \'%,' . $value . ',%\'';
                  } else {
                     $sql .= " FIND_IN_SET('" . $value . "', " . $colname . ") ";
                  }
               } else {
                  $sql .= " OR " . $colname . " = '" . $value . "' ";
               }

            }
            $sql = " (" . trim(ltrim($sql), "OR") . ") ";
         } else {
            $sql = " " . $colname . " = '" . $src[$sign] . "' ";
            if ($this->getFindInSetMode($dbname)) {
               if ($this->getDb()->isMssql()) {
                  $sql = ' \',\'+' . $colname . ' +\',\' like \'%,' . $src[$sign] . ',%\'';
               } else {
                  $sql = " FIND_IN_SET('" . $src[$sign] . "', " . $colname . ") ";
               }
            }
         }
         return $sql;
      }
      if (isset($this->validateRulesMapping["rules"][$this->getEditPrefix() . $dbname]["required"])&&$this->validateRulesMapping["rules"][$this->getEditPrefix() . $dbname]["required"]) {
         $this->validateRulesMapping["rules"][$this->getEditPrefix() . $dbname]["min"] = 1;
      }
       $this->setCustomJs("initCol_" .  $className, $js);
      return $result;
   }
      
        public function addAttachDataBySqlArray($db,$sqlArray)
        {
            $dataMsg = new DataMsg();
            $array = $dataMsg->findByMultiSql($db,$sqlArray,true);
            $this->attachData = array_merge($this->attachData,$array);
        }


        


         public function setCheckRule($name,$rule,$errorMsg=null,$emptyMsg=null)
         {
             if($rule!=null&&trim($rule)!="")
             {
                $sign = $this->getEditPrefix().$name;
                $this->checkRule[$name] =  Array("rule" => $rule,"errorMsg"=>$errorMsg,"emptyMsg"=>$emptyMsg,);
             }
         }

         public function getCheckRuleJs($formid)
         {
            $js = "";
            if(is_array($this->checkRule)&&count($this->checkRule)>0)
            { 
             $js .= '<script type="text/javascript">$("#'.$formid.'").validate(';
              $rules ="";
              $messages ="";  
              foreach($this->checkRule as $name => $rule)
              { 
                $name = $this->getEditPrefix().$name;
                $rules.=$name.":{".$rule["rule"]."},";
                if($rule["errorMsg"]!=null&&trim($rule["errorMsg"])!="")
                {
                    if($rule["emptyMsg"]==null||trim($rule["emptyMsg"])=="")
                    {
                        $rule["emptyMsg"] = $rule["errorMsg"];
                    }
                    $messages.=$name.":[\"".$rule["emptyMsg"]."\",\"".$rule["errorMsg"]."\"],";
                }
              }
              $rules = trim($rules,",");
              $messages = trim($messages,",");
              $temp ="";
              if(trim($rules)!="")
              {
                  $temp.= "rules:{".$rules."}";
              }
              if(trim($messages)!="")
              {
                  if($temp!="")
                  {
                      $temp.=",";
                  }
                  $temp.="messages:{".$messages."}";
              }
              if($temp!=null&&trim($temp)!="")
              {
                 $js.="{".$temp."}";  
              }
              $js.=  ');</script>';
            }
            return $js;
          
   
         }
         public function setIsExport($isExport)
         {
              $this->isExport =$isExport ;
         }
         public function isExport()
         {
             return  $this->isExport ;
         }
         public function setExportFileName($exportFileName)
         {
            $this->exportFileName = $exportFileName;
         }
         public function getExportFileName($format=null,$check=true)
         {  
        
            if($check)
            {

                $format = $this->getExportFormatString($format);
            }
            $format = strtolower($format);
        
            return $this->exportFileName.".".$format;
         }
         public function setAttachJs($dbname,$js,$search=true,$edit=true,$quickEdit=true)
         {
            if($search)
            {
                $this->attachJsArray["search"][$dbname] = $js;
            }
            if($edit)
            {
                 $this->attachJsArray["edit"][$dbname]= $js;
            }
            if($quickEdit)
            {
                 $this->attachJsArray["quickEdit"][$dbname] = $js;
            }
               
         }
         public function getAttachJs($type,$includeTag=true)
         {
            $array =  ArrayTools::getValueFromArray($this->attachJsArray,$type);
            $result = " ";
            if(is_array($array)&&count($array)>0)
            {
                $result = "";
               
                foreach($array as $key=>$js)
                {
                    if($js!=null&&trim($js)!="")
                    {
                        if(!StringTools::isEndWith($js,";"))
                        {
                            $js.=";";
                        }
                        $result.=$js;
                    }
                }
               if($includeTag)
               {
                  $result ='<script type="text/javascript">'.$result.'</script>';
               }
            }
            return $result;
         }
         public function setAttr($dbname,$key,$value,$search=true,$edit=true,$quickEdit=true)
         {

            if($search)
            {
                $this->attrArray[$dbname]["search"][$key] = $value;
            }
            if($edit)
            {
                 $this->attrArray[$dbname]["edit"][$key] = $value;
            }
            if($quickEdit)
            {
                 $this->attrArray[$dbname]["quickEdit"][$key] = $value;
            }
           

         }
         public function setIsExportWithTitle($isExportWithTitle)
         {
            $this->isExportWithTitle = $isExportWithTitle;
         }
         public function isExportWithTitle()
         {
            return   $this->isExportWithTitle;
         }
         public function setIsExportMode($isExportMode)
         {
            $this->isExportMode = $isExportMode;
         }
         public function isExportMode()
         {
            return   $this->isExportMode;
         }
      public function setSql($sql,$clearColInfo=false)
      {
        $this->sql = $sql;
        if($clearColInfo)
        {
            $this->colinfo = null;
        }
      }

      public function getSql($src)
      { 
        $result =  $this->sql;
        foreach($this->sqlGroup as $dbname =>$sqlInfo)
        {
            $sign = $this->getSearchPrefix().$dbname;
            $value = trim($src[$sign]);
            if($value!=null&&trim($value)!="")
            {
                $sql = $this->sqlGroup[$dbname][$value];
                if($sql!=null&&trim($sql)!="")
                {
                    $result = $sql;
                    break;
                }
            }
        }
        return $result;
      }
        public function setColInfo($col,$realCol)
        {
            if($this->colinfo==null)
            {
                $this->colinfo = Array();
            }
            $this->colinfo[$col] = $realCol;

        }
        public function getColInfo($src=null)
        {   
            if($this->colinfo==null)
            {
                  
                if($src==null)
                {
                  $src = $this->getDataSrc();
                }
                $sql = $this->getSql($src);

                if($sql!=null&&trim($sql)!="")
                {
                    $this->colinfo  = DbTools::getColNames($this->getSql($src));
                }
                else
                {
                    $this->colInfo = Array();
                }
            }
            return $this->colinfo;
        }

         public function setEditDefaultValueMap($editDefaultValueMap)
         {
              $this->editDefaultValueMap = $editDefaultValueMap;
         }


         public function getEditDefaultValueMap()
         {
              return $this->editDefaultValueMap;
         }

         public function getEditDefaultValue($dbname)
         {
           $result = null;
           if($this->editDefaultValueMap!=null&&is_array($this->editDefaultValueMap)&&isset($this->editDefaultValueMap[$dbname]))
           {
                 $result = $this->editDefaultValueMap[$dbname];
           }
           return $result;
         }
 
         public function setEditDefaultValue($dbname,$value="")
         {
   
              if($value ==null)
              {
                $this->removeEditDefaultValue($dbname);
              }
              else
              {
                  $this->editDefaultValueMap[$dbname] = $value;
              }
         } 
       
         public function removeEditDefaultValue($dbname)
         {
               unset($this->editDefaultValueMap[$dbname]);
         }

         public function setMethodResult($methodResult)
         {
            $this->methodResult = $methodResult;
         }
         
         public function getMethodResult()
         {
              return $this->methodResult;
         }
         public function setEditPrefix($editPrefix)
         {
            $this->editPrefix = $editPrefix;
         }

      

                   
         public function getEditPrefix()
         {
             return $this->editPrefix;
         }

         public function setSearchBar($searchBar)
         {
             $this->searchBar = $searchBar;
             $this->setClear($searchBar);
         }

         public function getSearchBar()
         {

            return  $this->searchBar;
         }

         public function getSearchId($dbname)
         {
            return $this->getSearchPrefix().$dbname;
         }

         public function getEditId($dbname)
         {
              return $this->getEditPrefix().$dbname;
         }
         
         public function setExport($export)
         {
             $this->export = $export;
         }
         
         public function setClear($clear)
         {
               $this->clearButton = $clear;
         }
         
         public function getClear()
         {  
             return $this->clearButton;
         }
         
         public function getExport()
         {
             return $this->export;
         }
         
         public function setPageExport($pageexport)
         {
             $this->pageexport = $pageexport;
         }
         
         public function getPageExport()
         {
             return ($this->pageexport&&$this->getResultSize()>0);
         }
         
         public function setToolbar($leftMethod=null,$rightMethod=null)
         {
             if($leftMethod!=null||$rightMethod!=null)
             {
                 $this->toolbar = Array(
                         "l" =>$leftMethod,
                         "r" =>$rightMethod,
                 );
                 
             }
         }
         public function getToolBar()
         {
             return  $this->toolbar;
         }
         
         public function addTopHtml($html)
         {
             $this->topHtml[] = $html;
         }

        public function addEditTopHtml($html)
         {
             $this->editTopHtml[] = $html;
         }

         public function addLayoutTopHtml($html)
         {
             $this->layoutTopHtml[] = $html;
         }

         public function getLayoutTopHtml()
         {
              return $this->layoutTopHtml;
         }

         public function getEditTopHtml()
         {
              return $this->editTopHtml;
         }

         public function addViewTopHtml($html)
         {
             $this->editTopHtml[] = $html;
         }

         public function getViewTopHtml()
         {
              return $this->editTopHtml;
         }

         public function addEditBottomHtml($html)
         {
             $this->editButtomHtml[] = $html;
         }

         public function addLayoutBottomHtml($html)
         {
             $this->layoutBottomHtml[] = $html;
         }


         public function addEditButtomHtml($html)
         {
             $this->editButtomHtml[] = $html;
         }

         public function getLayoutBottomHtml()
         {
              return $this->layoutBottomHtml;
         }

         public function getEditBottomHtml()
         {
              return $this->editButtomHtml;
         }

         public function getEditButtomHtml()
         {
              return $this->editButtomHtml;
         }

          public function addViewBottomHtml($html)
         {
             $this->editButtomHtml[] = $html;
         }

         public function addViewButtomHtml($html)
         {
             $this->editButtomHtml[] = $html;
         }

         public function getViewBottomHtml()
         {
              return $this->editButtomHtml;
         }
         
         public function getViewButtomHtml()
         {
              return $this->editButtomHtml;
         }
         
         public function addBottomHtml($html)
         {
              $this->buttomHtml[] = $html;
         }

         
         public function addButtomHtml($html)
         {
              $this->buttomHtml[] = $html;
         }

         public function addEditBottonHtml($html)
         {
              $this->editButtonHtml[] = $html;
         }

         public function addLayoutBottonHtml($html)
         {
              $this->layoutButtonHtml[] = $html;
         }

         public function addEditButtonHtml($html)
         {
              $this->editButtonHtml[] = $html;

         }

         public function getEditBottonHtml()
         {

             return $this->editButtonHtml;
         }
 
          public function getLayoutButtonHtml()
         {
             return $this->layoutButtonHtml;
         }

         public function getEditButtonHtml()
         {
             return $this->editButtonHtml;
         }

          public function addViewBottonHtml($html)
         {
              $this->editButtonHtml[] = $html;
         }

        public function addViewButtonHtml($html)
         {
              $this->editButtonHtml[] = $html;
         }

         public function getViewBottonHtml()
         {
             return $this->editButtonHtml;
         }
         public function getViewButtonHtml()
         {
             return $this->editButtonHtml;
         }



         public function getTopHtml()
         {
             return $this->topHtml;
         }

          public function getBottomHtml()
         {
             return $this->buttomHtml;
         }
         
         public function getButtomHtml()
         {
             return $this->buttomHtml;
         }

          public function setUploadPrefix($uploadPrefix)
         {
            $this->uploadPrefix = $uploadPrefix;
         }
                   
         public function getUploadPrefix()
         {
             return $this->uploadPrefix;
         }

         public function setSearchPrefix($searchPrefix)
         {
            $this->searchPrefix = $searchPrefix;
         }
                   
         public function getSearchPrefix()
         {
             return $this->searchPrefix;
         }
         public function addEditHidden($id,$value="")
         {
            $id = $this->getEditPrefix().$id;
            $this->addHidden($id,$value);
         }
         public function addSearchHidden($id,$value)
         {
            $id = $this->getSearchPrefix().$id;
            $this->addHidden($id,$value);
         }

         public function addLayoutHidden($id,$value)
         {
             $html = new HtmlElement($id,$id);
             $result = $html->getHidden($value);
             if(!is_array($this->hidden))
             {
                $this->layoutHidden = array();
             }
             $this->layoutHidden[$id] = $result;
         }

         public function getLayoutHiddenStr($hidden=null)
         {  
             $result = "";
             if($layoutHidden==null)
             {
                $layoutHidden = $this->layoutHidden;
             }
             if(is_array($layoutHidden))
             {
               foreach($layoutHidden as $id=>$str)
               {
                   $result .= $str; 
               }
             }
             return $result;
         }

         public function getLayoutHidden()
         {
            $layoutHidden = $this->layoutHidden;
            if(!is_array($layoutHidden))
            {
                $layoutHidden = array();
            } 
            return $layoutHidden;
         }

         public function addHidden($id,$value="")
         {
             $html = new HtmlElement($id,$id);
             $result = $html->getHidden($value);
             if(!is_array($this->hidden))
             {
                $this->hidden = array();
             }
             $this->hidden[$id] = $result;
         }
         public function getHidden()
         {
            $hidden = $this->hidden;
            if(!is_array($hidden))
            {
                $hidden = array();
            } 
            return $hidden;
         }
         public function getHiddenStr($hidden=null)
         {  
             $result = "";
             if($hidden==null)
             {
                $hidden = $this->hidden;
             }

             if(is_array($hidden))
             {
               foreach($hidden as $id=>$str)
               {
                   $result .= $str; 
               }
             }
             return $result;
         }
         
         public function addJsFile($url,$charest="utf-8")
         {
            if(!isset($this->jsFile[$url]))
            {
                $this->jsFile[$url] = $charest;
            }
         }
         public function addCssFile($url)
         {
             if(!in_array($url,$this->cssFile))
            {
                $this->cssFile[] = $url;
            }
         }
         public function setMethod($method,$methodName,$check=false)
         {  
            if(!isset($this->methodArray[$method])||!$check)
            {
              $this->methodArray[$method][$methodName]= $methodName;
            }
         }
          public function addMethod($method,$methodName,$check=false)
         {  
            if(!isset($this->methodArray[$method][$methodName])||!$check)
            {
              $this->methodArray[$method][$methodName]= $methodName;
            }
         }

        
 

       
         
         public function execMethod($db,$method,$src)
         {
             $methodNameArray = ArrayTools::getValueFromArray($this->methodArray,$method);
             $result = true;
             if(isset($this->methodArray[$method])&&is_array($this->methodArray[$method]))
             {
                foreach($methodNameArray as $methodName =>$methodName2)
                {
                     if($methodName!=null&&trim($methodName)!=""&&method_exists($this,$methodName))
                     {
                        if($result)
                        {
                            $result = $this->$methodName($db,$src);
                        }
                        else
                        {
                            break;
                        }
                     }
                }
             }
             $this->methodResult = $result;
             return $this->methodResult;
         }
             
         public function getScriptStr()
         {
            $result = "";
            foreach($this->jsFile as $url => $charest)
            {
               $result  .='<script type="text/javascript" language="javascript" src="'.$url.'" charest="'.$charest.'"></script>';
            }
            foreach($this->cssFile as $url)
            {
                $result .= '<link rel="stylesheet" href="'.$url.'">';
            }
            $result.="<script> 
                     function _randomString(len) {
                     　　len = len || 32;
                     　　var chars = 'ABCDEFGHJKMNPQRSTWXYZabcdefhijkmnprstwxyz2345678';  
                     　　var maxPos = chars.length;
                     　　var pwd = '';
                     　　for (i = 0; i < len; i++) {
                     　　　　pwd += chars.charAt(Math.floor(Math.random() * maxPos));
                     　　}
                     　　return pwd;
                     }
                     </script>";
            //$result.=$this->getCustomJs(true);
            $result.=$this->getAttachJs("search",true);
            $result.=$this->getAttachJs("quickEdit",true);
            return $result;
         }
         public function addLinkField($dbname,$linksign,$linkkey,$displayName="",$multiValueInRow=false,$spiltBy=",",$oriDbName=null,$isFromDB=true,$isCanSearch=true,$isChecked=true)
         {
            $this->addField($dbname,$displayName,$oriDbName,$isFromDB,$isCanSearch,$isChecked,true,$linksign,$multiValueInRow,$spiltBy,$linkkey);
         }
         public function addMultiField($dbname,$displayName="",$spiltBy=",",$oriDbName=null,$isFromDB=true,$isCanSearch=true,$isChecked=true)
         {
            $this->addField($dbname,$displayName,$oriDbName,$isFromDB,$isCanSearch,$isChecked,false,null,true,$spiltBy);
         }

         public function setFieldAttr($dbname,$attr,$value)
         {
               $this->fields[$dbname][$attr] = $value;
         }
         public function addField($dbname,$displayName="",$oriDbName=null,$isFromDB=true,$isCanSearch=true,$isChecked=true,$linkfield=false,$linksign=null,$multiValueInRow=false,$spiltBy=",",$linkkey="")
         {
             
             $this->fieldsOrder[] = $dbname;
             $colinfo = $this->getColInfo();
             if($oriDbName!=null)
             {
                    $this->setColInfo($dbname,$oriDbName); 
             }
             if($oriDbName==null)
             {
                 
                 if(is_array($colinfo)&&isset($colinfo[$dbname])&&$colinfo[$dbname]!=null&&trim($colinfo[$dbname])!="")
                 {
                    $oriDbName = $colinfo[$dbname];
                 }
                 else
                 {
                   $oriDbName = $dbname;
                 }
             }
             
             $this->fields[$dbname] = Array(
                 "displayname"=>$displayName,
                 "isfromdb"=>$isFromDB,
                 "iscansearch"=>$isCanSearch,
                 "ischecked"=>$isChecked,
                 "oridbname"=>$oriDbName,
                 "islinkfield"=>$linkfield,
                 "linkdatasign" =>$linksign,
                 "ismultivalueinrow" => $multiValueInRow,
                 "spiltby" => $spiltBy,
                 "linkkey" => $linkkey,
             );
         }

         
         
          public function initEdit($src=null)
          {
              
          }
         
         
          public function initBase($src=null,$isReport=true)
          {
                  $this->hasCheckBox = null;
                  $this->adFields = Array();     
                  $this->userSearchShowMode = Array();
                  $this->userExportShowMode = Array();
                  $this->userEditShowMode = Array();
                  $this->userReportShowMode = Array();
                  $this->defaultSearchShowMode = Array();
                  $this->defaultExportShowMode = Array();
                  $this->defaultReportShowMode = Array();
                  $this->jsFile = Array();
                  $this->methodArray = Array();
                  $this->fields = Array();
                  $this->fieldsOrder = Array();
                  $this->hidden = Array();
                  $this->topHtml = Array();
                  $this->buttomHtml = Array();
                  $this->editTopHtml = Array();
                  $this->editButtonHtml = Array();
                  $this->editDefaultValueMap = array();
                  $this->toolbar = null;
                  $this->export = true;
                  $this->pageexport = true;
                  $this->clear = true;
                  $this->regMagicFunction("getAjaxTagBy","getCommonAjaxTagBy");
                  $this->regMagicFunction("getMultiAjaxTagBy","getCommonMultiAjaxTagBy");
                  $this->regMagicFunction("getImagePickerBy","getCommonImagePickerBy");
                  $this->regMagicFunction("getAjaxDataBy","getCommonAjaxDataBy");
                  $this->regMagicFunction("getSelectBy","getCommonSelectBy");
                  $this->regMagicFunction("getAjaxSelectBy","getCommonAjaxSelectBy");
                  $this->regMagicFunction("getAutoCompleteBy","getCommonAutoCompleteBy");
                  $this->regMagicFunction("getSoundWarningSelectBy","getCommonSoundWarningSelectBy");
                  $this->regMagicFunction("getSmartSelectBy","getCommonSmartSelectBy");
         
                //  $this->regMagicFunction("getSelect2By","getCommonSelect2By");
               //  $this->regMagicFunction("getMultiSelect2By","getCommonMultiSelect2By");
                  $this->regMagicFunction("getMultiSelectBy","getCommonMultiSelectBy");
                   $this->regMagicFunction("getMultiAjaxSelectBy","getCommonMultiAjaxSelectBy");
                  $this->regMagicFunction("getCheckboxesBy","getCommonCheckboxesBy"); 
                  $this->regMagicFunction("translateBy","getCommonTranslateBy"); 
                  $this->regMagicFunction("getTextAreaWithClass","getCommonTextAreaWithClass");
                  $this->regMagicFunction("getRadiosBy","getCommonRadiosBy");
                  $this->regMagicFunction("getTreeBy","getCommonTreeBy");
                  $this->regMagicFunction("getAjaxTreeBy","getCommonAjaxTreeBy");
                  $this->regMagicFunction("getTagBy","getCommonTagBy");
                  $this->regMagicFunction("getMultiTagBy","getCommonMultiTagBy");
                  $this->regMagicFunction("getChartDataFrom","getCommonChartDataFrom");
                  $this->regMagicFunction("getStatDataFrom","getCommonStatDataFrom");
                  if($isReport)
                  { 
                      $this->getBaseShowMode();
                  }
                
          }

       public function getCommonChartDataFrom($name,$arguments)
        {
                $keyStr = substr($name,strlen("getChartDataFrom"));     
                $array = $arguments[0];
                return $array[$keyStr];
         }
         public function getCommonStatDataFrom($name,$arguments)
        {
                $keyStr = substr($name,strlen("getStatDataFrom"));     
                $array = $arguments[0];
                return $array[$keyStr];
         }
              

          protected  function getBaseShowMode()
          {
                  $this->addDefaultExportShowMode("Text","defaultShowMode");
                  $this->addDefaultReportShowMode("Text","defaultShowMode");
                  $this->addDefaultExportShowMode("Dollar","getDollar");
                  $this->addDefaultReportShowMode("Dollar","getDollar");
                  $this->addDefaultExportShowMode("DatePicker(YYYY-MM-DD)","dataPickerReportMode"); 
                  $this->addDefaultReportShowMode("DatePicker(YYYY-MM-DD)","dataPickerReportMode");
                  $this->addDefaultSearchShowMode("DataPicker(YYYY-MM-DD)","datePickerShowMode");
                  $this->addDefaultSearchShowMode("MonthPicker(YYYY-MM)","monthPickerShowMode");
                  $this->addDefaultSearchShowMode("DateTimePicker(YYYY-MM-DD HH:MM)","datetimePickerShowMode");
                  $this->addDefaultSearchShowMode("AccountidFromSession(Hidden)","  ");
                  $this->addDefaultSearchShowMode("DatePicker(YYYY-MM-DD%)","datePickerStratWithShowMode");
                  $this->addDefaultSearchShowMode("DatePicker(YYYY-MM%)","datePickerMonthStratWithShowMode");
                  $this->addDefaultSearchShowMode("DateRange(YYYY-MM-DD&E)","DataRangeShowMode");
                  $this->addDefaultSearchShowMode("Text(Equal)","equalTextSearchShowMode");
                  $this->addDefaultSearchShowMode("Text(Like)","defaultSearchShowMode");
                  $this->addDefaultSearchShowMode("Text(StartWith)","startWithSearchShowMode");
                  $this->addDefaultSearchShowMode("Text(Readonly&Equal)","readOnlyEqualSearchShowMode");
                  $this->addDefaultSearchShowMode("Text(Readonly&Like)","readOnlySearchShowMode");
                  $this->addDefaultSearchShowMode("Text(Readonly&startWith)","readOnlyStartWithSearchShowMode");
                  $this->addDefaultSearchShowMode("ValueRange(with equal)","valueRangeShowMode");
                  $this->addDefaultSearchShowMode("ValueRange(without equal)","valueRangeWithOutEqualShowMode");
                  $this->addDefaultSearchShowMode("textWithHidden","textWithHiddenSearchShowMode");
                  $this->addDefaultEditShowMode("textWithHidden","textWithHiddenSearchShowMode");
                  $this->addDefaultEditShowMode("DataPicker(YYYY-MM-DD)","datePickerShowMode");
                  $this->addDefaultEditShowMode("AccountidFromSession(Hidden)","sessionAccountidSearchShowMode");
                  $this->addDefaultEditShowMode("Text","defaultSearchShowMode");
                  $this->addDefaultEditShowMode("Text(Readonly)","readOnlySearchShowMode");
                  
          }
          

          public function setDebug($debug)
          {
             $this->debug = $debug;
          }
          public function getDebug()
          {
            return $this->debug;
          }
          public function getOriDbName($dbname,$withTableKey=false)
          {
              $field = $this->getFieldByName($dbname);
              $colname = $field["oridbname"];
              if($withTableKey)
              {
                $colname = DbTools::getColNameFormCol($colname);
              }
              return $colname;
          }

          public function getOriTableKey($dbname)
          {
              $colname = $this->getOriDbName($dbname);
              return  DbTools::getTableKeyFormCol($colname);
          }
      
          public function getDefaultValue($dbname)
          {
              $field = $this->getSavedField($dbname);
              $defaultValue = $field["defaultsearch"];
              return $defaultValue;
          }
          
          public function preLoad($db,$src=null)
          {
              
          }

          public function beforeLoad($db,$src=null)
          {
              
          }
          public function afterLoad($db,$src=null)
          {
              
          }
      
          public function processData($db,$src,$dataArray,$edit=false)
          {
              return $dataArray;
          }
          public function isEditMode()
          {
              $result = false;
              if($this->getSearchPrefix()==$this->getEditPrefix()||$this->getSearchPrefix()==$this->getQuickEditPrefix())
              {
                 $result = true;
              }
              return $result;
          }

          public function getEditDivId($dbname)
          {
                $editDivId = "div_edit_".$dbname;
                return  $editDivId;
          }

          public function getEditDivHtml($dbname,$html)
          {
             $id = $this->getEditDivId($dbname);
             $div = new HtmlElement($id,$id);
             $div->setParam("width","100%");
             $div->setParam("height","100%");
             return $div->getDiv($html);
          }
          
          public function showEditShowMode($methodname,$save,$upload,$dbname,$src,$value=null,$isAjax=false)
          {   
              //debug_print_backtrace();
              if($value == null)
              { 
            
                  if($src!=null&&is_array($src))
                  {
                    if(!$this->fields[$dbname]["islinkfield"])
                    {
                        if(isset($src[$dbname]))
                        {
                            $value = $src[$dbname];
                        }
                    }
                    else
                    {
                        $dataSign = $this->fields[$dbname]["linkdatasign"];

                        $srcdbname = $this->linkPath[$dataSign]["srcdbname"];
                   
                        if(isset($src[$srcdbname]))
                        {
                            $ismultivalueinrow = $this->fields[$srcdbname]["ismultivalueinrow"];
                            
                            $tmp = strval($src[$srcdbname]);

                            if($tmp!=null&&trim($tmp)!="")
                            {
                                $arr = Array();
                                if(!$ismultivalueinrow)
                                {
                                    $arr = array_merge($arr,$this->linkData[$dataSign]["value"][$tmp]);
                                  
                                }
                                else
                                {
                                    $spiltBy = $this->fields[$srcdbname]["spiltby"];
                                    $tmpArr = explode($tmp, $spiltBy);
                                    foreach($tmpArr as $t)
                                    {   
                                        $arr = array_merge($arr,$this->linkData[$dataSign]["value"][$t]);
                                    }

                                }
                                $linkkey = $this->fields[$dbname]["linkkey"];
                                foreach($arr as $a)
                                {
                                    if($a[$linkkey]!=null&&trim($a[$linkkey])!="")
                                    {
                                        $value .=$this->fields[$dbname]["spiltby"].$a[$linkkey];
                                    }
                                } 
                                if($value!=null&&trim($value!=""))
                                {
                                    $value = ltrim($value,$this->fields[$dbname]["spiltby"]);
                                }
                            }
                        }
                    }
                  }
              }
            
              if($value==null&&$this->getEditDefaultValue($dbname)!=null)
              {
                  $value = $this->getEditDefaultValue($dbname);
              }
              $searchPrefix = $this->getSearchPrefix();
              $prefix = "";
              if($save)
              {
                $prefix = $this->getEditPrefix();
              }
              if($upload)
              {
                $prefix = $this->getUploadPrefix();
              }
              $this->setSearchPrefix($prefix);
              $colname = $this->getOriDbName($dbname);
              $html = $this->$methodname($dbname,$colname,$src,false,$value);
              $editTemplate = $this->getEditTemplate($dbname);

              if($editTemplate!=null&&trim($editTemplate)!="")
              {
                 $templateHtml = $this->getTemplate($editTemplate);
                 if($templateHtml!=null&&trim($templateHtml)!="")
                 {
               
                    $html = str_replace($this->getValueMark(),$html,$templateHtml);
                 }
              } 

              $this->applyEditFieldLink($dbname);
              $this->applyColMapping($dbname,"edit");
              $cssClass = "";
              if(isset($this->attrArray[$dbname]["edit"]["class"]))
              {
                  $cssClass = $this->attrArray[$dbname]["edit"]["class"];
              }
              $cssArray = explode(" ", $cssClass);
              if($cssClass!=null&&trim($cssClass)!="")
              {
                 $cssClass .= " ";
              }
              if(!in_array("form-control", $cssArray))
              { 
                if(!isset($this->voidBootStrap[$dbname])||!$this->voidBootStrap[$dbname])
                {
                    $cssClass .= "form-control";
                }
              }

              $this->attrArray[$dbname]["edit"]["class"] = $cssClass;
              $html = $this->addAttrJs($dbname, $html,"edit");
              $this->setSearchPrefix($searchPrefix);
              if(!$isAjax)
              {
                $html = $this->getEditDivHtml($this->getEditPrefix().$dbname,$html);
              }
              return $html;
          }

          public function getSpan($dbname,$arrayName,$html)
          {

          }
          protected function applyColMapping($dbname,$type,$quickEditPrefix=null,$keyValue=null,$mainid=null,$quickEditid=null)
          {
            $onblur  = "";
            $key = "onblur";
            $ajaxmethod = "";
            $prefix = "";
            $colMapping = null;
            $formid = null;
            $aeid = "ed_ajaxmethod";
            $typeid = "ed_colmappingtype";
            $keyid = "ed_colmappingkey";
            $mainidid = "ed_colmappingmainid";
            $realKey = "";
            $mainidValue = "";
            $quickEditidValue = "";
            if($type=="edit")
            {
                $formid = $this->getEditFormId();
                $prefix ="div_edit_";
                
                $colMapping = $this->editColMapping;
            }
            else if($type=="quickEdit")
            {
                $formid = "quickForm";
                $prefix = $quickEditPrefix;

                $quickEditidValue = $quickEditid;
               
                $colMapping = $this->editColMapping;
               

                if($keyValue!=null&&trim($keyValue)!="")
                {
                    $realKey = $keyValue; 
                }

                 if($mainid!=null&&trim($mainid)!="")
                {
                    $mainidValue = $mainid; 
                }
         
            }

            if(isset($colMapping[$dbname])&&$colMapping[$dbname]!=null&&trim($colMapping[$dbname])!="")
            {
                $ajaxmethod = $colMapping[$dbname];
               
                if($this->attrArray[$dbname][$type]!=null&&is_array($this->attrArray[$dbname][$type] ))
                {

                    foreach($this->attrArray[$dbname][$type] as $name =>$value)
                    {
                        if(strtoUpper(trim($name))=="ONBLUR")
                        {
                            $onblur = trim($value);
                            $key = $name;
                            break;
                        }
                    }
                }
                

                    if($type=="edit")
                    {
                      if(isset($this->editCustomField[$dbname]))
                       {
                            $sign = $dbname;
                       }
                       else
                       {
                            $sign = $this->getEditPrefix().$dbname;
                       }
                   } 

                 
                    $onblur = "_colMapping(this,'".$formid."','".$prefix."','".$aeid."','".$ajaxmethod."','".$typeid."','".$type."','".$keyid."','".$realKey."','".$mainidid."','".$mainidValue."','".$quickEditid."');";
               
                    if($key!=null&&trim($key)!=""&&$onblur!=null&&trim($onblur)!="")
                    {
                        $this->setAttr($dbname,$key,$onblur,false,true);
                        $this->setAttachJs($dbname,$onblur,false,true);
                    }       
            }
            else if($quickEditid!=null&&trim($quickEditid)!="")
            {
                 $js = "_quickEdit('".$quickEditid."','".QuickFormConfig::$quickFormMethodPath."quickEdit.php');";
                 $this->setAttr($dbname,"onChange",$js,false,true);

            }

          }
          protected function applyEditFieldLink($dbname)
          {
             $onblur  = "";
             $key = "onblur";
             $id = $this->getEditPrefix().$dbname;
             if(isset($this->editFieldLink[$dbname])&&is_array($this->editFieldLink[$dbname]))
            {
             foreach($this->attrArray[$dbname]["edit"] as $name =>$value)
             {
                if(strtoUpper(trim($name))=="ONBLUR")
                {
                    $onblur = trim($value);
                    $key = $name;
                    break;
                }
             }
             if($onblur!=null&&trim($onblur)!="")
             {
                if(!StringTools::isEndWith($onblur,";"))
                {
                    $onblur.=";";
                }
             }
              
               $valueStr = "";
                foreach($this->editField as $tmpdbname=>$data)
                {
                    $tmp = $this->getEditPrefix().$tmpdbname;
                    $valueStr .= "vajax_".$tmp.":function(){return $(\"".$tmp."\").val();},";
                }
                foreach($this->editCustomField as $id=>$data)
                {
                    $valueStr .= "vajax_".$id.":function(){return $(\"".$id."\").val();},";
                }
                foreach($this->editFieldLink[$dbname] as $dstname)
                {
                    if($this->editField[$dstname]!=null)
                    {
                        $dstname = $this->getEditPrefix.$dstname;
                    }
                    $onblur.= "$.post('".QuickFormConfig::$quickFormMethodPath."getFieldAjax.php',{".$valueStr."formmark:".$this->getFormMark().",isreport:".$this->isReport()."dbname:".$dbname."},function(result){
                         $('#".$this->getEditDivId($dstname)."').html(result);
                    });";
                }
            }
            if($key!=null&&trim($key)!=""&&$onblur!=null&&trim($onblur)!="")
            {
                $this->setAttr($dbname,$key,$onblur,false,true);
            }
          }
          protected function addAttrJs($dbname,$html,$type)
          { 
            
              if(is_array($this->attrArray[$dbname][$type])&&count($this->attrArray[$dbname][$type])>0)
              {  
                  $js = '<script type="text/javascript">';
                  $attr = null;
                  $css = null;
                  $onChange = null;
                  foreach($this->attrArray[$dbname][$type] as $key=>$value)
                  { 
                    if(trim(strtolower($key))!= "class" && trim(strtolower($key))!= "placeholder")
                    {
                        if(trim(strtolower($key))== "onchange")
                        {
                            $onBlur ='"onBlur":"'.$value.'"';
                        }
                        $attr.= '"'.$key.'":"'.$value.'",';
                    }
                    else if(trim(strtolower($key))== "placeholder")
                    {

                        if($this->colSetting[$dbname]["placeholder"]!=null&&trim($this->colSetting[$dbname]["placeholder"])!=""&&($value==null||$value==""))
                        {
                            $value = $this->colSetting[$dbname]["placeholder"];
                        }
                         $attr.= '"'.$key.'":"'.$value.'",';
                    }
                    else 
                    {
                        $css = $value;
                    }
                  }  
                  if($attr!=null&&trim($attr)!="")
                  {
                    $js.=   '$("#'. $this->getSearchPrefix().$dbname.'").attr({';
                    $js.=$attr;
                    $js.= "});";
                  }
                  if($type=="quickEdit"&&isset($this->colSetting[$dbname]["isChceckBoxWithHidden"])&&intval($this->colSetting[$dbname]["isChceckBoxWithHidden"])==1)
                  {
                     $js .= '$("#chk_'. $this->getSearchPrefix().$dbname.'").attr({';
                     $js.=$onBlur;
                     $js.= "});";
                  }
                  if($css!=null&&trim($css)!="")
                  {
                     $js.=   '$("#'. $this->getSearchPrefix().$dbname.'").addClass("'.trim($css).'")';
                  }
                  $js.=  '</script>';
                  $html .= $js;
              }
              return $html;
          }

          public function switchSearchShowMode($dbname,$colname,$src,$sql=false,$defaultValue="")
          {
             $switchText = $this->getSwitchText($dbname);
             return $this->checkBoxSearchShowMode($dbname,$colname,$src,$sql,$defaultValue,true,$switchText["onText"],$switchText["offText"]);
          }

          public function checkBoxSearchShowMode($dbname,$colname,$src,$sql=false,$defaultValue="",$isSwitch=false,$switchOnText=null,$switchOffText=null)
          {
               $sign = $this->getSearchPrefix().$dbname;  
               $this->colSetting[$dbname]["isChceckBoxWithHidden"] = 1;
               if($sql)
               {
                  $sql = " ".$colname." = '".$src[$sign]."' ";
                  return $sql;
               }
               $html = new HtmlElement($sign,$sign);
               $html = $this->checkBoxExtend($dbname,$html,$src);
               return $html->getCheckBoxWithHidden($defaultValue,1,0,null,$isSwitch,$switchOnText,$switchOffText);
          }

          public function checkBoxExtend($dbname,$html,$src=null)
          {
              return $html;
          }
          public function getSearchStatus()
          {
              $result = false;
              if($this->isSearch==1)
              {
                $result = true;
              }
              return $result;
          }
          public function showSearchShowMode($methodname,$searchSign=0,$dbname,$src,$sql=false,$defaultValue="",$showSearchBar=false,$searchPrefix=null) 
          { 


               $this->isSearch =  $searchSign;

               $dstdbname = $this->getSearchFieldMapping($dbname);
               if($dstdbname == null || trim($dstdbname) == "")
               {
                    $dstdbname  = $dbname;
               }

               if($searchPrefix==null||trim($searchPrefix)=="")
               {
                   $searchPrefix = $this->getSearchPrefix();
               }
               $fieldInfo = $this->fields[$dstdbname];
               $islinkfield = $fieldInfo["islinkfield"];
               $colname = null;
               if(!$islinkfield)
               {
                   $colname = $this->getOriDbName($dstdbname);
                   if(isset($this->searchField[$dstdbname]["oridbname"])&&$this->searchField[$dstdbname]["oridbname"]!=null&&trim($this->searchField[$dstdbname]["oridbname"])!="")
                   {
                        $colname = $this->searchField[$dstdbname]["oridbname"];
                   }
               }
               else
               {
                    $colname = $fieldInfo["linkdatasign"].".".$fieldInfo["linkkey"];
               }
               $sign = $searchPrefix.$dbname;
              
               if($searchSign==1||$showSearchBar)
               {
                   $sign = $searchPrefix.$dbname;     
                   $defaultValue = $src[$sign];
                   $searchMapping = $this->getSearchMapping($dbname);                    
                    if(($defaultValue==null||trim($defaultValue)=="")&&$searchMapping!=null&&trim($searchMapping)!=""&&trim($src[$searchMapping])!=null&&trim($src[$searchMapping])!="")
                    {
                        $defaultValue = $src[$searchMapping];
                    }
               }
               $prefix =$this->getSearchPrefix();
               $this->setSearchPrefix($searchPrefix);
               $html  = $this->$methodname($dbname,$colname,$src,$sql,$defaultValue);
               if(isset($this->attachHtml[$dbname]["search"])&&$this->attachHtml[$dbname]["search"]!=null&&trim($this->attachHtml[$dbname]["search"])!=""&&!$sql)
               {
                  
                  $html.= trim($this->attachHtml[$dbname]["search"]);
               }
               $this->setSearchPrefix($prefix);
               if(!$sql)
               {
                    
                    $template = $this->getSearchTemplate($dbname);
                      if($template!=null&&trim($template)!="")
                      {
                         $templateHtml = $this->getTemplate($template);
                         if($templateHtml!=null&&trim($templateHtml)!="")
                         {
                            $html = str_replace($this->getValueMark(),$html,$templateHtml);
                         }
                      }
                      $cssClass = "";
                      if(isset($this->attrArray[$dbname]["search"]["class"])) 
                      {
                       $cssClass = $this->attrArray[$dbname]["search"]["class"];
                      }
                      $cssArray = explode(" ", $cssClass);
                      if($cssClass!=null&&trim($cssClass)!="")
                      {
                         $cssClass .= " ";
                      }
                      if(!in_array("form-control", $cssArray))
                      { 
                         $cssClass .= "form-control";
                      }
       
                      $this->attrArray[$dbname]["search"]["class"] = $cssClass;
                      $html  = $this->addAttrJs($dbname,$html,"search");
               }

         
               
               return $html;
          }
   


          public function sessionAccountidSearchShowMode($dbname,$colname,$src,$sql=false,$defaultValue="")
          {
                if($sql)
                {                  
                    $sql = " ".$colname." = '".$_SESSION[USER_REF]->id."' ";
                    return $sql;
                }
                return null;
          }
          
          public function readOnlySearchShowMode($dbname,$colname,$src,$sql=false,$defaultValue="",$isDataPicker=false,$dtParams="")
          {
              
              $size = strlen($defaultValue);
              return $this->defaultSearchShowMode($dbname,$colname,$src,$sql,$defaultValue,$isDataPicker,$dtParams,$size,true);
          }
          
          public function readOnlyStartWithSearchShowMode($dbname,$colname,$src,$sql=false,$defaultValue="",$isDataPicker=false,$dtParams="")
          {
            
              $size = strlen($defaultValue);
              return $this->startWithSearchShowMode($dbname,$colname,$src,$sql,$defaultValue,$isDataPicker,$dtParams,$size,true);
          }
          
          public function readOnlyEqualSearchShowMode($dbname,$colname,$src,$sql=false,$defaultValue="",$isDataPicker=false,$dtParams="")
          {
              
              $size = strlen($defaultValue);
              return $this->equalTextSearchShowMode($dbname,$colname,$src,$sql,$defaultValue,$isDataPicker,$dtParams,$size,true);
          }
          public function hiddenSearchShowMode($dbname,$colname,$src,$sql=false,$defaultValue="")
          {
               $sign = $this->getSearchPrefix().$dbname;  
               if($sql)
               {
                  $sql = " ".$colname." = '".$src[$sign]."' ";
                  return $sql;
               }
               $html = new HtmlElement($sign,$sign);
               return $html->getHidden($defaultValue);
          }

          public function uploadEditShowMode($dbname,$colname,$src,$sql=false,$defaultValue="")
          {
               if($sql)
               {
                    $sql = " ";
                    return $sql;
               } 
               $sign = $this->getUploadPrefix().$dbname;
               $html = new HtmlElement($sign,$sign);
               return $html->getFile();
          }


          public function textWithHiddenSearchShowMode($dbname,$colname,$src,$sql=false,$defaultValue="")
          {
                $hidden = $this->hiddenSearchShowMode($dbname,$colname,$src,$sql,$defaultValue);
                return $defaultValue.$hidden;  
          }

          public function whereGroupSearchShowMode($dbname,$colname,$src,$sql=false,$defaultValue="")
          {
              $sign = $this->getSearchPrefix().$dbname;
              if($sql)
              {
                 $sql = " ".$this->whereGroup[$dbname][$src[$sign]]["where"]." ";
                 return $sql;
              } 
              $html = new HtmlElement($sign,$sign);
              $whereGroup = $this->whereGroup[$dbname];
              return $html->getSelectByMap($whereGroup,"name",$defaultValue);



          }

          public function smartInputSearchShowMode($dbname,$colname,$src,$sql=false,$defaultValue="")
          {
                $sign = $this->getSearchPrefix().$dbname; 
                $html = new HtmlElement($sign,$sign);
                $html->setParam("class","form-control");
                $setting = $this->getSmartInputSetting($dbname);

               
                if($setting["autoFocus"])
                {
                    $js.="$('#".$sign."')[0].focus();";
                }
                $js .="$('#".$sign."').keyup(function(){
                    var length = $(this).val().length;
                    if(length>".$setting["maxlength"]."){ $(this).val('');}else{
                    if(length>=".$setting["minlength"]."){
                     $.post('".QuickFormConfig::$quickFormMethodPath."checkSmartInputVal.php',{tablename:'".$setting["tablename"]."',findkey:'".$setting["findkey"]."',whereClause:'".$setting["whereClause"]."',value:$(this).val()},function(result){
                        if($.trim(result)=='clear')
                        {
                            $('#".$sign."').val('');
                            return false;
                        }
                        if($.trim(result)=='submit')
                        {
                            ".$setting["submitJs"]."
                        }
                     }); 

                   }}";

                $js .="})";
                $this->setCustomJs("initCol_".$dbname,$js);
                 if($sql)
                {
                    $sql = " ".$colname." like '%".$src[$sign]."%' ";
                    return $sql;
                }
                $result = $html->getInput($defaultValue);   
                return $result;


          }

          public function getCustomHtmlElement($dbname,$method,$defaultValue,$prefix="")
          {
              $searchPrefix = $this->getSearchPrefix();
              $editPrefix = $this->getEditPrefix();
              $this->setSearchPrefix($prefix);
              $this->setEditPrefix($prefix);
              $result = $this->$method($dbname,"",null,false,$defaultValue);   
              $this->setSearchPrefix($searchPrefix);
              $this->setEditPrefix($editPrefix);
              return $result;
          }
   
          public function currencySearchShowMode($dbname,$colname,$src,$sql=false,$defaultvalue="")
          {
             $sign = $this->getSearchPrefix().$dbname;    
                if($sql)
                {    
                    $sql = " ".$colname." = '".$src[$sign]."' ";
                    return $sql;
                }
                $html = '<input id="'.$sign.'" name="'.$sign.'" class="form-control" type="number" value="'.$defaultvalue.'" step="0.01"/>';
               return $html;

          } 

          public function currencyRangeSearchShowMode($dbname,$colname,$src,$sql=false,$defaultValue="",$isEqual=false)
          {
               $sign = $this->getSearchPrefix().$dbname; 
               $start = $sign;
               $end =  $sign."_end";
               $startValue = "";
               $endValue = "";
               if($this->isSearch==1)
               {
                   $startValue = $src[$start];
                   $endValue = $src[$end];
               }
               else
               {
                  $tmp = explode(",",$defaultValue);
                  $size = count($tmp);
                  if($size==1)
                  {
                      $startValue = $tmp[0]; 
                  }
                  else if($size==2)
                  {
                       $startValue = $tmp[0]; 
                       $endValue =$tmp[1];
                  }
               }
               $temp ="";
               if($isEqual)
               {
                     $temp ="=";
               }
               $timeStart="";
               $timeEnd="";
               $sign = "";
               
               if($sql)
               {    $sql = " 1=1 ";
                     if($startValue!=null&&trim($startValue)!="")       
                     {    
                       $curcolname =$colname; 
                       if($time)
                       {
                            $db = $this->getDb();
                            if($db->isMssql())
                            {
                                $curcolname = "SUBSTRING(".$colname.",1,".strlen($startValue).")";
                            }
                            else
                            {
                                 $curcolname = "SUBSTR(".$colname.",1,".strlen($startValue).")";
                            }
                            $sign ="'";
                       }   
                        $sql .= " AND ".$curcolname." >".$temp." ".$sign.$startValue.$timeStart.$sign." ";
                     }
                     if($endValue !=null&&trim($endValue)!="")
                     {
                         $curcolname =$colname; 
                       if($time)
                       {
                            $db = $this->getDb();
                            if($db->isMssql())
                            {
                                $curcolname = "SUBSTRING(".$colname.",1,".strlen($endValue).")";
                            }
                            else
                            {
                                 $curcolname = "SUBSTR(".$colname.",1,".strlen($endValue).")";
                            }
                            $sign ="'";
                       }   
                        $sql .= " AND ".$curcolname." <".$temp." ".$sign.$endValue.$timeEnd.$sign." ";
                     }
                    return $sql;
               }
               $startHtml = '<input id="'.$start.'" name="'.$start.'" class="form-control" type="number" value="'.$startValue.'" step="0.01"/>';      
               $result = "<div class='row'><div class='col-md-5'>".$startHtml."</div>";
               $endHtml = '<input id="'.$end.'" name="'.$end.'" class="form-control" type="number" value="'.$endValue.'" step="0.01"/>';             
               $result.= "<div align='center' class='col-md-2'><b>---</b></div><div class='col-md-5'>".$endHtml ."</div></div>";
               return $result;
          }

          public function numberRangeSearchShowMode($dbname,$colname,$src,$sql=false,$defaultValue="",$isEqual=false)
          {
               $sign = $this->getSearchPrefix().$dbname; 
               $start = $sign;
               $end =  $sign."_end";
               $startValue = "";
               $endValue = "";
               if($this->isSearch==1)
               {
                   $startValue = $src[$start];
                   $endValue = $src[$end];
               }
               else
               {
                  $tmp = explode(",",$defaultValue);
                  $size = count($tmp);
                  if($size==1)
                  {
                      $startValue = $tmp[0]; 
                  }
                  else if($size==2)
                  {
                       $startValue = $tmp[0]; 
                       $endValue =$tmp[1];
                  }
               }
               $temp ="";
               if($isEqual)
               {
                     $temp ="=";
               }
               $timeStart="";
               $timeEnd="";
               $sign = "";
               
               if($sql)
               {    $sql = " 1=1 ";
                     if($startValue!=null&&trim($startValue)!="")       
                     {    
                       $curcolname =$colname; 
                       if($time)
                       {
                            $db = $this->getDb();
                            if($db->isMssql())
                            {
                                $curcolname = "SUBSTRING(".$colname.",1,".strlen($startValue).")";
                            }
                            else
                            {
                                 $curcolname = "SUBSTR(".$colname.",1,".strlen($startValue).")";
                            }
                            $sign ="'";
                       }   
                        $sql .= " AND ".$curcolname." >".$temp." ".$sign.$startValue.$timeStart.$sign." ";
                     }
                     if($endValue !=null&&trim($endValue)!="")
                     {
                         $curcolname =$colname; 
                       if($time)
                       {
                            $db = $this->getDb();
                            if($db->isMssql())
                            {
                                $curcolname = "SUBSTRING(".$colname.",1,".strlen($endValue).")";
                            }
                            else
                            {
                                 $curcolname = "SUBSTR(".$colname.",1,".strlen($endValue).")";
                            }
                            $sign ="'";
                       }   
                        $sql .= " AND ".$curcolname." <".$temp." ".$sign.$endValue.$timeEnd.$sign." ";
                     }
                    return $sql;
               }
               $numberFormatSetting = $this->getNumberFormatSetting($dbname);
               $html = new HtmlElement($start,$start);
               $html->setParam("class","form-control");
               $startHtml = $html->getNumber($startValue,$numberFormatSetting);      
               $result = "<div class='row'><div class='col-md-5'>".$startHtml."</div>";
               $html = new HtmlElement($end,$end);
               $html->setParam("class","form-control");
               $endHtml = $html->getNumber($endValue,$numberFormatSetting); 
               $result.= "<div align='center' class='col-md-2'><b>---</b></div><div class='col-md-5'>".$endHtml ."</div></div>";
               return $result;
          }

           public function numberSearchShowMode($dbname,$colname,$src,$sql=false,$defaultvalue="")
          {

             $sign = $this->getSearchPrefix().$dbname;    
                if($sql)
                {    
                    $sql = " ".$colname." = '".$src[$sign]."' ";
                    return $sql;
                }
                $html = new HtmlElement($sign,$sign);
                $html->setParam("class","form-control");
                $numberFormatSetting = $this->getNumberFormatSetting($dbname);
               return $html->getNumber($defaultvalue,$numberFormatSetting);

          } 

       
          public function defaultSearchShowMode($dbname,$colname,$src,$sql=false,$defaultValue="",$isDataPicker=false,$dtParams="",$size=null,$readOnly=false)
          {
                $sign = $this->getSearchPrefix().$dbname;    
                if($sql)
                {    
                    $sql = " ".$colname." like '%".$src[$sign]."%' ";
                    return $sql;
                }
                $html = new HtmlElement($sign,$sign);
                if($isDataPicker)
                {
                   $html->setFunction("onClick","WdatePicker(".$dtParams.")"); 
                   $html->setParam("class","Wdate form-control");
                   $html->setParam("autocomplete","off");
                   $size=10;
                }
                else
                {
                    $html->setParam("class","form-control");
                }
                if($size==null&&isset($this->colSetting[$dbname]["size"])&&$this->colSetting[$dbname]["size"]!=null&&intval($this->colSetting[$dbname]["size"])>0)
                {
                   $size = intval($this->colSetting[$dbname]["size"]);
                }
                if($size!=null)
                {
                   $html->setParam("size",$size);
                }
                if($readOnly)
                {
                   $html->setParam("readonly","readonly");
                }
                return $html->getInput($defaultValue);         
          }

          public function passwordSearchShowMode($dbname,$colname,$src,$sql=false,$defaultvalue="",$params=null)
          {
              $sign = $this->getSearchPrefix().$dbname; 
              if($sql)
              {
                  $sql = " ".$colname." like '".$src[$sign]."%' ";
                  return $sql;
              }
              
              $html = new HtmlElement($sign,$sign);
              if(is_array($params))
              {
                 foreach($params as $key=>$value)
                 {
                    $html->setParam($key,$value);
                 }
              }
              $result = $html->getPasswordInput($defaultvalue);
              $this->passwordFieldMapping[$dbname]["password"] =  $sign;     
              return $result;
          }

          public function confirmPasswordSearchShowMode($dbname,$colname,$src,$sql=false,$defaultvalue="",$params=null)
          {
              $sign = $this->getSearchPrefix().$dbname; 
             
              $tsign = "pwt_".$sign;
              $ti = new HtmlElement($tsign,$tsign);
              if(is_array($params))
              {
                 foreach($params as $key=>$value)
                 {
                    $ti->setParam($key,$value);
                 }
              }
              $this->passwordFieldMapping[$dbname]["passwordagain"] =  $tsign; 
              $placeholder = "";
              if($this->colSetting[$dbname]["placeholder"]!=null&&trim($this->colSetting[$dbname]["placeholder"])!="")
                {
                    $placeholder = $this->colSetting[$dbname]["placeholder"];
                }
              $js = '$("#'.$tsign.'").attr({"placeholder":"'.$placeholder.'","class":"form-control",});';
              $this->setCustomJs("initCol_".$dbname,$js);
               if($sql)
              {
                 return " 1=1";
              }
              return $ti->getPasswordInput($defaultvalue);
          }
          
          public function startWithSearchShowMode($dbname,$colname,$src,$sql=false,$defaultValue="",$isDataPicker=false,$dtParams="",$size=null)
          {
                $sign = $this->getSearchPrefix().$dbname;               
                if($sql)
                {                  
                    $sql = " ".$colname." like '".$src[$sign]."%' ";
                    return $sql;
                }
                $html = new HtmlElement($sign,$sign);
              
                if($isDataPicker)
                {
                   $html->setFunction("onClick","WdatePicker(".$dtParams.")"); 
                   $html->setParam("class","Wdate form-control");
                   $size=10;
                }
                else
                {
                    $html->setParam("class","form-control");
                }
                if($size!=null)
                {
                   $html->setParam("size",$size);
                }
                return $html->getInput($defaultValue);    
          }

          public function multiLineSearchShowMode($dbname,$colname,$src,$sql=false,$defaultValue="")
          {

             $sign = $this->getSearchPrefix().$dbname;    
             if($sql)
             {
                $value = $src[$sign];
                $array = explode("\n",$value);
                $ids = "";
                foreach($array as $a)
                {
                    if($a!=null&&trim($a)!="")
                    {
                      $ids.=",'".trim($a)."'";
                    }
                }
                $ids =trim($ids,",");
                $sql = " ".$colname." in (".$ids.")";
                return $sql;
             }
             $multiSetting = $this->getMultiLineSetting($dbname);
             $size = $multiSetting["size"];
             $cols = $multiSetting["cols"];
             return $this->textAreaSearchShowMode($dbname,$colname,$src,$sql,$defaultValue,false,$size,$cols);

          }

          public function multiLineLikeSearchShowMode($dbname,$colname,$src,$sql=false,$defaultValue="")
          {

             $sign = $this->getSearchPrefix().$dbname;    
             if($sql)
             {
                $value = $src[$sign];
                $array = explode("\n",$value);
                $sql = "";
                foreach($array as $a)
                {
                     if($a!=null&&trim($a)!="")
                    {
                      $sql.=" OR ".$colname." LIKE '%".trim($a)."%'";
                    }
                }
                $sql = trim($sql);
                $sql = ltrim($sql,"OR");
                return $sql;
             }
             return $this->multiLineSearchShowMode($dbname,$colname,$src,$sql,$defaultValue,false,10);
          }


           public function multiLineMultiWordLikeSearchShowMode($dbname,$colname,$src,$sql=false,$defaultValue="")
          {

             $sign = $this->getSearchPrefix().$dbname;    
             if($sql)
             {
                $value = $src[$sign];
                $array = explode("\n",$value);
                $sql = "";
                foreach($array as $a)
                {
                     if($a!=null&&trim($a)!="")
                    {
                      $a_array = explode(" ", $a);
                      $searchStr = "%";
                      foreach($a_array as $at)
                      {
                         $searchStr .=trim($at)."%";
                      }
                      $sql.=" OR ".$colname." LIKE '".$searchStr."'";
                    }
                }
                $sql = trim($sql);
                $sql = ltrim($sql,"OR");
                return $sql;
             }
             return $this->multiLineSearchShowMode($dbname,$colname,$src,$sql,$defaultValue,false,10);
          }
      
          public function textAreaSearchShowMode($dbname,$colname,$src,$sql=false,$defaultValue="",$isEditor=false,$size=20,$cols=100,$class=null)
          {
                $sign = $this->getSearchPrefix().$dbname;               
                if($sql)
                {                  
                    $sql = " ".$colname." = '".$src[$sign]."' ";
                     if($this->getFindInSetMode($dbname))
                    {
                        if($this->getDb()->isMssql())
                        {
                             $sql = ' \',\'+'.$colname.' +\',\' like \'%,'.$src[$sign].',%\'';
                        }
                        else
                        {
                            $sql = " FIND_IN_SET('".$src[$sign]."', ". $colname.") ";
                        }
                    }
                    return $sql;
                }
                $html = new HtmlElement($sign,$sign);
                if($class!=null&&trim($class)!="")
                {
                    $html->setParam("class",$class);
                }
                else
                {
                     $class = $this->getColClass($dbname);
                     if($class!=null&&trim($class)!="")
                    {
                        $html->setParam("class",$class);
                    }
                    else if (!$isEditor)
                    {
                     $html->setParam("class","form-control");
                    }
                }
                $result = $html->getTextArea($defaultValue,$size,$cols);
               
                if($isEditor)
                {
                    $this->setVoidBootStrap($dbname,true);
                    $editorid = $dbname."_editor";
                    $result .= '<script type="text/javascript">';
                    $editorWidth = "100%";
                    $tmpWidth = $this->getEditorWidth($dbname);
                    if($tmpWidth!=null&&trim($tmpWidth)!="")
                    {
                        $editorWidth  = $tmpWidth;
                    }
                    $tmpHeight = $this->getEditorHeight($dbname);
                    $heightStr = "";
                     if($tmpHeight!=null&&trim($tmpHeight)!="")
                    {
                        $heightStr = ",initialFrameHeight:'".$tmpHeight."' ";
                    }
                    $result .= "var ".$editorid." = UE.getEditor('".$this->getEditPrefix().$dbname."', {serverUrl: '".QuickFormConfig::$ueditPath."/php/controller.php',initialFrameWidth:'".$editorWidth."'".$heightStr." });";
                    $result .="</script>";
                    $editInputButton = $this->editInputButton[$dbname];
                    $buttonStr = "";
                    if(is_array($editInputButton) && count($editInputButton)>0)
                    {

                       foreach($editInputButton as $echo=>$value)
                       {
                                $buttonid = $editorid."_".$echo."_button";
                                $html = new HtmlElement($buttonid, $buttonid);
                                $jsstr = $editorid.".execCommand('inserthtml', '".$value."');";
                                $html->setFunction("onClick",$jsstr);
                                $buttonStr.=$html->getButton($echo)."&nbsp;&nbsp;";
                       }
                    }
                    if(trim($buttonStr)!="")
                    {
                        $result.=$buttonStr;
                    }
                    
                }
                return $result;
          }

          public function EditorSearchShowMode($dbname,$colname,$src,$sql=false,$defaultValue="")
          {

             return  $this->textAreaSearchShowMode($dbname,$colname,$src,$sql,$defaultValue,true);
          }
          public function DateTimeRangePickerShowMode($dbname,$colname,$src,$sql=false,$defaultValue="",$secondPicker=false)
          {
      
                  return $this->DateRangePickerShowMode($dbname,$colname,$src,$sql,$defaultValue,true,$secondPicker);
          }
            public function DateTimeRangeWithSecondPickerShowMode($dbname,$colname,$src,$sql=false,$defaultValue="")
          {
                  return $this->DateTimeRangePickerShowMode($dbname,$colname,$src,$sql,$defaultValue,true);
          }
          public function DateRangePickerShowMode($dbname,$colname,$src,$sql=false,$defaultValue="",$timePicker=false,$secondPicker=false)
          {
            
                 $sign = $this->getSearchPrefix().$dbname; 
                 $needAnd = false;
                 if($sql)
                 {
                    $sql = "";
                    $value = $src[$sign];
                    $array = explode("--",$value);
                    $arrlen  = count($array);
                    if($arrlen>1)
                    {
                        $start = trim($array[0]);
                        if($start!=null&&trim($start)!="")
                        {
                              $sql .=" LEFT(".$colname.",".strlen($start).") >= '".$start."' ";
                              $needAnd = true;
                        }
                        if($arrlen==2)
                        {
                             if($needAnd)
                             {
                               $sql.=" AND ";
                             }
                             $end = trim($array[1]);
                             if($end!=null&&trim($end)!="")
                             {
                               $sql .=" LEFT(".$colname.",".strlen($end).") <= '".$end."' "; 
                             }
                        }
                    }
                    return $sql;
                 }
                 else
                 {
                        $options ="";
                        if($secondPicker)
                        {
                           $timePicker = true;
                        }

                        $html = new HtmlElement($sign,$sign); 
                        $html->setParam("autocomplete","off");
                        $ret = $html->getInput($defaultValue);
                        $format = 'YYYY-MM-DD';
                        $options .="";
                        if($timePicker)
                        {  
                           $format .=" HH:mm";
                           $options .="timePicker: true,timePicker24Hour: true,";
                           if($secondPicker)
                           {
                              $format.=":ss";
                              $options.="timePickerSeconds:true,";
                           }
                        }
                        $ret.="<script>
                           $('#".$sign."').daterangepicker({
                            autoUpdateInput: false,
                            alwaysShowCalendars: true,
                            ".$options."
                            locale: {
                                        cancelLabel: 'Clear', 
                                        format: '$format'
                                    },

                                      ranges: {
           'Today': [moment().startOf('day'), moment().endOf('day')],
           'Yesterday': [moment().subtract(1, 'days').startOf('day'), moment().subtract(1, 'days').endOf('day')],
           'This Week': [moment().startOf('week').add(1, 'days'), moment().endOf('week').add(1, 'days')],
           'Last Week': [moment().subtract(1, 'week').startOf('week').add(1, 'days'), moment().subtract(1, 'week').endOf('week').add(1, 'days')],
           'This Month': [moment().startOf('month'), moment().endOf('month')],
           'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')],
           'This Quarter': [moment().startOf('quarter'), moment().endOf('quarter')],
           'Last Quarter': [moment().subtract(1, 'quarter').startOf('quarter'), moment().subtract(1, 'quarter').endOf('quarter')],
           'This Year': [moment().startOf('year'), moment().endOf('year')],
           'Last Year': [moment().subtract(1, 'year').startOf('year'), moment().subtract(1, 'year').endOf('year')],
        }
                           });
                        $('#".$sign."').on('apply.daterangepicker', function(ev, picker) {
                              $(this).val(picker.startDate.format('".$format."') + '--' + picker.endDate.format('".$format."'));
                              });

                        $('#".$sign."').on('cancel.daterangepicker', function(ev, picker) {
                               $(this).val('');
                           });
                           </script>";
                       return $ret;
                 }
          }
          public function MultiDatePickerShowMode($dbname,$colname,$src,$sql=false,$defaultValue="")
          {
            $sign = $this->getSearchPrefix().$dbname; 
             if($sql)
             {
                $value = $src[$sign];
                $array = explode(",",$value);
                $ids = "";
                foreach($array as $a)
                {
                    if($a!=null&&trim($a)!="")
                    {
                      $ids.=",'".trim($a)."'";
                    }
                }
                $ids =trim($ids,",");
                $sql = " DATE_FORMAT(".$colname.",'%Y-%m-%d') in (".$ids.")";
                return $sql;
             }
             else
             {
            
               $html = new HtmlElement($sign,$sign);
               $html->setFunction("onClick","$(this).multiDatesPicker('value', $(this).val());");
               $ret = $html->getInput($defaultValue);

               $array = explode(",",$defaultValue);
               $defaultDate = "";
               if(count($array)>0&&trim($array[0])!="")
               {
                 $defaultDate = "defaultDate: \"".$array[0]."\",";
               }
               $setting = "";
               if(isset($this->multiDatePickerSetting[$dbname]["numberOfMonths"])&&is_array($this->multiDatePickerSetting[$dbname]["numberOfMonths"])&&count($this->multiDatePickerSetting[$dbname]["numberOfMonths"])==2)
               {
                  
                          $setting.="numberOfMonths:[".implode(",",$this->multiDatePickerSetting[$dbname]["numberOfMonths"])."],";
               }
               $ret.="<script>
                           $('#".$sign."').multiDatesPicker({
                             dateFormat: \"yy-mm-dd\",
                            ".$defaultDate.$setting."
                           });</script>";
                return $ret;  
             }
          }

          public function equalTextSearchShowMode($dbname,$colname,$src,$sql=false,$defaultValue="",$isDataPicker=false,$dtParams="",$size=null)
          {
                $sign = $this->getSearchPrefix().$dbname;               
                if($sql)
                {                  
                    $sql = " ".$colname." = '".$src[$sign]."' ";
                    if($this->getFindInSetMode($dbname))
                    {
                        if($this->getDb()->isMssql())
                        {
                            $sql = ' \',\'+'.$colname.' +\',\' like \'%,'.$src[$sign].',%\'';
                        }
                        else
                        {
                            $sql = " FIND_IN_SET('".$src[$sign]."', ". $colname.") ";
                        }
                    }
                    return $sql;
                }
                $html = new HtmlElement($sign,$sign);
               
                if($isDataPicker)
                {
                   $html->setFunction("onClick","WdatePicker(".$dtParams.")"); 
                   $html->setParam("class","Wdate form-control");
                   $html->setParam("style","min-width:100px");
                   $size=10;
                }
                else
                {
                     $html->setParam("class","form-control");
                }
                if($size!=null)
                {  
                   
                   $html->setParam("size",$size);
                }
                return $html->getInput($defaultValue);         
          }
          
          public function datePickerShowMode($dbname,$colname,$src,$sql=false,$defaultValue="",$dtParams="")
          {
              return $this->equalTextSearchShowMode($dbname,$colname,$src,$sql,$defaultValue,true,$dtParams);
          }
          public function monthPickerShowMode($dbname,$colname,$src,$sql=false,$defaultValue="",$dtParams="")
          {
              $dateFormat = $this->getDateFormat($dbname,'yyyy-MM');
              $dtParams = "{dateFmt:'".$dateFormat."'}";
              return $this->equalTextSearchShowMode($dbname,$colname,$src,$sql,$defaultValue,true,$dtParams);
          }



          public function datetimePickerShowMode($dbname,$colname,$src,$sql=false,$defaultValue="",$dtParams="")
          {
              $dateFormat = $this->getDateFormat($dbname,'yyyy-MM-dd HH:mm');
              $dtParams = "{dateFmt:'".$dateFormat."'}";
              return $this->equalTextSearchShowMode($dbname,$colname,$src,$sql,$defaultValue,true,$dtParams);
          }
          
           public function datePickerStratWithShowMode($dbname,$colname,$src,$sql=false,$defaultValue="",$dtParams="")
          {
              return $this->startWithSearchShowMode($dbname,$colname,$src,$sql,$defaultValue,true);
          }

          public function datePickerMonthStratWithShowMode($dbname,$colname,$src,$sql=false,$defaultValue="",$dtParams="")
          {
              $dateFormat = $this->getDateFormat($dbname,'yyyy-MM');
              $dtParams = "{dateFmt:'".$dateFormat."'}";
              return $this->startWithSearchShowMode($dbname,$colname,$src,$sql,$defaultValue,true,$dtParams);
          }
           public function DateRangeShowMode($dbname,$colname,$src,$sql=false,$defaultValue="",$isEqual=true,$dataFormat="yyyy-MM-dd")
           {
              return $this->DataRangeShowMode($dbname,$colname,$src,$sql,$defaultValue,$isEqual,$dataFormat,"Y-m-d");
           }
             public function DateTimeRangeWithSecondShowMode($dbname,$colname,$src,$sql=false,$defaultValue="",$isEqual=true,$dataFormat="yyyy-MM-dd HH:mm:ss")
           {
               return $this->DataRangeShowMode($dbname,$colname,$src,$sql,$defaultValue,$isEqual,$dataFormat,"Y-m-d h:i");
           }

           public function DateTimeRangeShowMode($dbname,$colname,$src,$sql=false,$defaultValue="",$isEqual=true,$dataFormat="yyyy-MM-dd HH:mm")
           {
               return $this->DataRangeShowMode($dbname,$colname,$src,$sql,$defaultValue,$isEqual,$dataFormat,"Y-m-d h:i");
           }

           public function MonthRangeShowMode($dbname,$colname,$src,$sql=false,$defaultValue="",$isEqual=true,$dataFormat="yyyy-MM")
           {
                return $this->DataRangeShowMode($dbname,$colname,$src,$sql,$defaultValue,$isEqual,$dataFormat,"Y-m");
           }

          public function DataRangeShowMode($dbname,$colname,$src,$sql=false,$defaultValue="",$isEqual=true,$dataFormat="yyyy-MM-dd",$phpDateFormat="Y-m-d")
          {
              $dataFormat = $this->getDateFormat($dbname,$dataFormat);
              $sign = $this->getSearchPrefix().$dbname; 
              $start = $sign;
              $end =  $sign."_end";  
              $src[$start] = $this->getTimeForShow($dbname,$src[$start],false,$phpDateFormat);
              $src[$end] = $this->getTimeForShow($dbname,$src[$end],false,$phpDateFormat);
              $dtParamsStart = "{maxDate:'#F{\$dp.\$D(\'".$end."\')}',dateFmt:'".$dataFormat."'}";
              $dtParamsEnd = "{minDate:'#F{\$dp.\$D(\'".$start."\')}',dateFmt:'".$dataFormat."'}";
              return $this->valueRangeShowMode($dbname,$colname,$src,$sql,$defaultValue,$isEqual,true,$dtParamsStart,$dtParamsEnd,null,true);
          }
          
          
          public function valueRangeWithOutEqualShowMode($dbname,$colname,$src,$sql=false,$defaultValue="",$isDataPicker=false,$dtParamsStart="",$dtParamsEnd="")
          {
              return $this->valueRangeShowMode($dbname,$colname,$src,$sql,$defaultValue,false,$isDataPicker,$dtParamsStart,$dtParamsEnd);
          }
          public function valueRangeShowMode($dbname,$colname,$src,$sql=false,$defaultValue="",$isEqual=true,$isDataPicker=false,$dtParamsStart="",$dtParamsEnd="",$size=null,$time=false)
          {
               $sign = $this->getSearchPrefix().$dbname; 
               $start = $sign;
               $end =  $sign."_end";
               $startValue = "";
               $endValue = "";
               if($this->isSearch==1)
               {
                   $startValue = $src[$start];
                   $endValue = $src[$end];
               }
               else
               {
                  $tmp = explode(",",$defaultValue);
                  $size = count($tmp);
                  if($size==1)
                  {
                      $startValue = $tmp[0]; 
                  }
                  else if($size==2)
                  {
                       $startValue = $tmp[0]; 
                       $endValue =$tmp[1];
                  }
               }
               $temp ="";
               if($isEqual)
               {
                     $temp ="=";
               }
               $timeStart="";
               $timeEnd="";
               $sign = "";
               
               if($sql)
               {    $sql = " 1=1 ";
                     if($startValue!=null&&trim($startValue)!="")       
                     {    
                       $curcolname =$colname; 
                       if($time)
                       {
                            $db = $this->getDb();
                            if($db->isMssql())
                            {
                                $curcolname = "SUBSTRING(".$colname.",1,".strlen($startValue).")";
                            }
                            else
                            {
                                 $curcolname = "SUBSTR(".$colname.",1,".strlen($startValue).")";
                            }
                            $sign ="'";
                       }   
                        $sql .= " AND ".$curcolname." >".$temp." ".$sign.$startValue.$timeStart.$sign." ";
                     }
                     if($endValue !=null&&trim($endValue)!="")
                     {
                         $curcolname =$colname; 
                       if($time)
                       {
                            $db = $this->getDb();
                            if($db->isMssql())
                            {
                                $curcolname = "SUBSTRING(".$colname.",1,".strlen($endValue).")";
                            }
                            else
                            {
                                 $curcolname = "SUBSTR(".$colname.",1,".strlen($endValue).")";
                            }
                            $sign ="'";
                       }   
                        $sql .= " AND ".$curcolname." <".$temp." ".$sign.$endValue.$timeEnd.$sign." ";
                     }
                    return $sql;
               }
               $html = new HtmlElement($start,$start);
               if($isDataPicker)
               {
                   $html->setFunction("onClick","WdatePicker(".$dtParamsStart.")"); 
                    $html->setParam("class","Wdate form-control");
                   $size=10;
               }
               else
               {
                    $html->setParam("class","form-control");
               }

               if($size!=null)
               {
                   $html->setParam("size",$size);
               }
               $result = "<div class='row'><div class='col-md-5'>".$html->getInput($startValue)."</div>";
               $html = new HtmlElement($end,$end);           
               if($isDataPicker)
               {
                   $html->setFunction("onClick","WdatePicker(".$dtParamsEnd.")"); 
                    $html->setParam("class","Wdate form-control");
                   $size=10;
               }
               else
               {
                    $html->setParam("class","form-control");

               }
                if($size!=null)
               {
                 $html->setParam("size",$size);
               }
               $result.= "<div align='center' class='col-md-2'><b>---</b></div><div class='col-md-5'>".$html->getInput($endValue)."</div></div>";
               return $result;
          }
          
          public function defaultShowMode($row,$dbname,$export=false)
          {
                
                return $this->getValueByDbName($row,$dbname,$export);   
          }
          public function DateTimeShowMode($row,$dbname,$export=false)
          {
                $format = $this->getDateFormat($dbname,"Y-m-d H:i:s");
                $value = $this->getValueByDbName($row,$dbname,$export);
                return $this->getTimeForShow($dbname,$value,$format);
          }
          public function editUrlReportMode($row,$dbname,$export=false)
          {
             $result =  $this->getValueByDbName($row,$dbname,false);  
             if(!$export)
             {
                $mainId = $this->getValueByDbName($row,$this->getMainIdDbName(),false);
                $url = "#";
                $html = new HtmlElement();
                $html->setFunction("onClick","_editItem('".$mainId."','')"); 
                $result = $html->getUrl($result,$url);
             }
             return $result;
          }
          public function dataPickerReportMode($row,$dbname,$export=false)
          {
              $value =  $this->getValueByDbName($row,$dbname,false);  
              if($export)
              {
                  return $value;
              }
              $htmlId = $this->getHtmlId($row,$dbname);
              $html = new HtmlElement($htmlId,$htmlId);
              $html->setFunction("onClick","WdatePicker()"); 
              $html->setParam("class","Wdate form-control");
              $html->setParam("size",10);
              $html->setParam("style","min-width:100px");
              return $html->getInput($value);
          }
    
         protected function getHtmlId($row,$dbname)
         {
             return $dbname."_".$row;
         }
         
         
         public function getField($i)
         {
             return $this->getFieldByName($this->getFieldName[$i]);
         }
         public function getFieldByName($dbname)
         {
            return $this->fields[$dbname];    
         }
        
         public function getFieldName($i)
         {
             return $this->fieldsOrder[$i];
         }  
         public function getFieldSize()
         {
             return count($this->fieldsOrder);
         }
         
         
         public function getDefaultSearchShowModeSize()
         {
             return count($this->defaultSearchShowMode());
         }
         
         public function getDefaultExportShowModeSize()
         {
             return count($this->defaultExportShowMode());
         }
         
         public function getDefaultReportShowModeSize()
         {
             return count($this->defaultReportShowMode());
         }

         public function getDefaultEditShowModeSize()
         {
             return count($this->defaultEditShowMode());
         }

         public function getDefaultEditShowMode()
         {
             return $this->defaultEditShowMode;
         }
         
         public function getDefaultSearchShowMode()
         {
             return $this->defaultSearchShowMode;
         }
         
         public function getDefaultExportShowMode()
         {
             return $this->defaultExportShowMode;
         }
         
         public function getDefaultReportShowMode()
         {
             return $this->defaultReportShowMode;
         }
        
         public function addDefaultEditShowMode($name,$showMode)
         {
             $this->defaultEditShowMode[$name] = $showMode;
         }

         public function addDefaultSearchShowMode($name,$showMode)
         {
             $this->defaultSearchShowMode[$name] = $showMode;
         }
         
         public function addDefaultExportShowMode($name,$showMode)
         {
             $this->defaultExportShowMode[$name] = $showMode;
         }
         
         public function addDefaultReportShowMode($name,$showMode)
         {
             $this->defaultReportShowMode[$name] = $showMode;
         }
         
         public function getSearchShowMode($dbname)
         {
             return array_merge($this->getUserSearchShowMode($dbname),$this->getDefaultSearchShowMode());
         }
         public function getEditShowMode($dbname)
         {
             return array_merge($this->getUserEditShowMode($dbname),$this->getDefaultEditShowMode());
         }
         public function getExportShowMode($dbname)
         {
             return array_merge($this->getUserExportShowMode($dbname),$this->getDefaultExportShowMode());
         }
         public function getReportShowMode($dbname)
         {
             return array_merge($this->getUserReportShowMode($dbname),$this->getDefaultReportShowMode());
         }
         public function getSearchShowModeSize($dbname)
         {
             return $this->getDefaultSearchShowModeSize()+$this->getUserSearchShowModeSize($dbname);
         }
         public function getExportShowModeSize($dbname)
         {
             return $this->getDefaultExportShowModeSize()+$this->getUserExportShowModeSize($dbname);
         }

         public function getEditShowModeSize($dbname)
         {
             return $this->getDefaultEditShowModeSize()+$this->getUserEditShowModeSize($dbname);
         }
         public function getReportShowModeSize($dbname)
         {
             return $this->getDefaultReportShowModeSize()+$this->getUserReportShowModeSize($dbname);
         }
         
          
         function __construct()
         {
             $this->savedFields = Array();
             $this->userSearchShowMode = Array();
             $this->userExportShowMode = Array();
             $this->userReportShowMode = Array();
             $this->userEditShowMode = Array();
             $this->defaultSearchShowMode = Array();
             $this->defaultExportShowMode = Array();
             $this->defaultReportShowMode = Array();
             $this->defaultEditShowMode = Array();
             $this->fields = Array();
             $fieldsOrder = Array();
            
         }
         
         public function getSfvString($dbname,$key,$defaultValue="",$allowEmpty=false)
         {
             $result = $defaultValue; 
             $field = $this->getSavedField($dbname);
             if($field!=null&&$field[$key]!=null)
             {
                 if($allowEmpty||(!$allowEmpty&&trim($field[$key])!=""))
                 {
                    $result = $field[$key];
                 }
             }
             return $result;
         }
         
         public function getSfvInt($dbname,$key,$defaultValue=0)
         {
             $result = $defaultValue;
             $field = $this->getSavedField($dbname);
             if($field!=null&&$field[$key]!=null)
             {
                  $result = intval($field[$key]); 
             }
             return $result;
         }
         public function getFields()
         {
            return $this->fields;
         }
         public function setFields($fields)
         {
             $this->fields = $fields;
         }
         public function setSavedFields($savedFields)
         {
             $this->savedFields =  $savedFields;
         }
         public function getSavedFields()
         {
             return   $this->savedFields ;
         }
         public function getSizeofSavedFields()
         {
             return   count($this->savedFields);
         }
         public function getSavedField($dbname){
             return $this->savedFields[$dbname];
         }
         public function addSavedField($dbname,$field)
         {
              $this->savedFields[$dbname] = $field;
         }
         public function getUserSearchShowMode($dbname)
         {
           if($this->userSearchShowMode!=null&&$this->userSearchShowMode[$dbname]!=null)
           {
             return $this->userSearchShowMode[$dbname];
           }
           return Array();
         }
         public function getUserSearchShowModeSize($dbname)
          {
             if($this->userSearchShowMode!=null&&$this->userSearchShowMode[$dbname]!=null)
             {
                return count($this->userSearchShowMode[$dbname]);
             }
             return 0;
         }
         public function addUserSearchShowMode($dbname,$name,$methodName)
         {
             if($this->userSearchShowMode==null)
             {
                 $this->userSearchShowMode = Array();
             }
             $this->userSearchShowMode[$dbname][$name]= $methodName;
         }
         
         public function getUserExportShowMode($dbname)
         {
           if($this->userExportShowMode!=null&&$this->userExportShowMode[$dbname]!=null)
           {
             return $this->userExportShowMode[$dbname];
           }
           return Array();
         }
         public function getUserExportShowModeSize($dbname)
          {
             if($this->userExportShowMode!=null&&$this->userExportShowMode[$dbname]!=null)
             {
                return count($this->userExportShowMode[$dbname]);
             }
             return 0;
         }
         public function getUserEditShowMode($dbname)
         {
           if($this->userEditShowMode!=null&&$this->userEditShowMode[$dbname]!=null)
           {
             return $this->userEditShowMode[$dbname];
           }
           return Array();
         }
         public function getUserEditShowModeSize($dbname)
          {
             if($this->userEditShowMode!=null&&$this->userEditShowMode[$dbname]!=null)
             {
                return count($this->userEditShowMode[$dbname]);
             }
             return 0;
         }
         public function addUserExportShowMode($dbname,$name,$methodName)
         {
             if($this->userExportShowMode==null)
             {
                 $this->userExportShowMode = Array();
             }
             $this->userExportShowMode[$dbname][$name] =$methodName;
         }
         
          public function getUserReportShowMode($dbname)
         {
           if($this->userReportShowMode!=null&&$this->userReportShowMode[$dbname]!=null)
           {
             return $this->userReportShowMode[$dbname];
           }
           return Array();
         }
         public function getUserReportShowModeSize($dbname)
          {
             if($this->userReportShowMode!=null&&$this->userReportShowMode[$dbname]!=null)
             {
                return count($this->userReportShowMode[$dbname]);
             }
             return 0;
         }
         public function addUserReportShowMode($dbname,$name,$methodName)
         {
             if($this->userReportShowMode==null)
             {
                 $this->userReportShowMode = Array();
             }
             $this->userReportShowMode[$dbname][$name] = $methodName;
         } 
        public function getCustomSqlForAdvanceSearch($dbname)
        {
           $startSign = "SELECT";
           $stopSign = "FROM";
           $colname = $this->searchField[$dbname]["oridbname"];
           if($colname==null||trim($colname)=="")
           {
               $colInfo = $this->getColInfo();
               $colname = $colInfo[$dbname];
           }
           $sql = $this->getSql();
           $sql = trim($sql);
           $sql = str_replace(PHP_EOL, ' ', $sql);   
           $arr = explode(" ",$sql);
           $tmp = "";
           $add = false;
           $inpart = 0;
           $result = "SELECT ".$colname;
           $add = false;
           foreach ($arr as $value) {
               if(trim(strtoupper($value))==$stopSign&&$inpart == 0)
               {
                  $add = true;
               }
               if($add)
               {
                  $result .=" ".$value." ";
               }
           }
           return $result;
        }
    }
?>