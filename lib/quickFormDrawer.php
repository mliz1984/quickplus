<?php
namespace Quickplus\Lib;
use Quickplus\Lib\Tools\StringTools;
use Quickplus\Lib\DataMsg\DataMsg;
use Quickplus\Lib\Tools\ArrayTools;
class quickFormDrawer 
{
        protected $quickForm;
        protected $reportName;
        protected $blank = null;
        protected $where = null;
        public function setChartFilter($chartId)
        {
            $this->quickForm = $this->quickForm->initChartFilter($chartId);
        }
         public function setDashboardFilter($dashboardId)
        {
            $this->quickForm = $this->quickForm->initDashboardFilter($dashboardId);
        }
          public function setStatisticFilter($chartId)
        {
            $this->quickForm = $this->quickForm->initStatisticFilter($chartId);
        }
        public function setWhere($whereClause,$replace=true,$relation="AND")
        {
            $this->where = Array("whereClause"=>$whereClause,"replace"=>$replace,"relation"=>$relation);
        }
        public function getWhere()
        {
            return  $this->where;
        }
        public function setBlank($blank)
        {
            $this->blank = $blank;
        }
        public function getReportName()
        {
            return $this->reportName;
        }
        public function setReportName($reportName)
        {
            $this->reportName = $reportName;
        }
        public function getQuickForm()
        {
            return $this->quickForm;
        }
        protected function checkRightAccess($db,$src=null)
        {
           if(QuickFormConfig::$quickFormRightControl)
           {
              $checkLogin = false;
              $url = $_SERVER['PHP_SELF'];
              $loginManager  = QuickLoginManager::getQuickLoginManager();
              $method = null;
              if(isset($src["method"]))
              {
                  $method = $src["method"];
              }
               if(!$loginManager->checkRight($url,$method,$src))
                  {
                    $loginManager->goToErrorPage();
                  }
                  $checkLogin = true;
           }
           return $checkLogin;
        }

        public function setQuickForm($db,$quickForm,$src=null,$edit=true)
        {
            if($src==null)
            {
                $src = $_REQUEST;
            }
            $quickForm->setDataSrc($src);
            if($quickForm->getLoginCheck())
            {
                $this->checkRightAccess($db,$src);
            }
            if($quickForm->getDb()==null)
            {
               $quickForm->setDb($db);
            }
            else
            {
                $db = $quickForm->getDb();
            }
            $quickForm->prepare($src);
            $quickForm->preLoad($db,$src);
            $initMethod = $quickForm->getInitMethod($src);
            $initEditMethod = $quickForm->getInitEditMethod($src);
            $initLayoutMethod = $quickForm->getInitLayoutMethod($src);
  
            $customInitMethod = $quickForm->getCustomInitMapping($src);
            if($customInitMethod!=null&&is_array($customInitMethod))
            {
                     $initMethod = $customInitMethod["init"];
                     $initEditMethod = $customInitMethod["initEdit"];
                     $initLayoutMethod = $customInitMethod["initLayout"];
            }  
            $quickForm->setDefaultExportFormat(QuickFormConfig::$defaultExportFormat);
            if(!$quickForm->isReport())
            {
          
              $quickForm->initBase($src,$quickForm->isReport());
            }  
            if($quickForm->isLayout()&&$initLayoutMethod!=null)
            {
      
               $quickForm->$initLayoutMethod($src);
            }
            if($edit&&$initEditMethod!=null)
            {
         
                $quickForm->$initEditMethod($src);
            }    
            $quickForm->initCustomProcessMethod($src);
            if(!$quickForm->isReport()&&$initMethod!=null)
            {
                $quickForm->$initMethod($src);
            }

       
            $quickForm->initCustomCol($src);
            $quickForm->initAutoRefresh($src);
            $quickForm->getAllLinkPath(); 
           
            if($quickForm->isLayoutAdd()||$quickForm->isLayoutCopy())
            {
                $quickForm->setMethod("layoutadd","insertFormData",true);
            }
            if($quickForm->isLayoutDelete())
            {
                $quickForm->setMethod("layoutdelete","deleteFormDataByMainId",true);
            }
            if($quickForm->isLayoutModify())
            {
                $quickForm->setMethod("layoutupdate","updateFormData",true);
            }

            if($quickForm->isDelete())
            {
                $quickForm->setMethod("delete","deleteFormDataByMainId",true);
            }
            if($quickForm->isEdit($src))
            {
                $quickForm->setMethod("update","updateFormData",true);
            }
             if($quickForm->isAdd())
            {
                $quickForm->setMethod("add","insertFormData",true);
            }
            if($quickForm->isDelete())
            {
                $quickForm->setMethod("delete","deleteFormDataByMainId",true);
            }
            if($quickForm->isEdit($src))
            {
                $quickForm->setMethod("update","updateFormData",true);
            }
            $quickForm->initCustomMethod($src);
     
            $quickForm->addJsFile(QuickFormConfig::$jquery);
            $quickForm->addCssFile(QuickFormConfig::$jqueryUiPath."themes/base/jquery.ui.all.css");
            $quickForm->addJsFile(QuickFormConfig::$jqueryUiPath."ui/jquery-ui.js");
            $quickForm->addJsFile(QuickFormConfig::$datePickerPath."WdatePicker.js");
            $quickForm->addJsFile(QuickFormConfig::$jqueryValidationPath."jquery.validate.min.js");  
            $quickForm->addJsFile(QuickFormConfig::$jqueryValidationPath."lib/jquery.form.js");  
            $quickForm->addJsFile(QuickFormConfig::$quickFormResourcePath."jQuery.print.min.js"); 
            $quickForm->addJsFile(QuickFormConfig::$quickFormResourcePath."bootstrap/js/bootstrap.min.js"); 
            $quickForm->addCssFile(QuickFormConfig::$quickFormResourcePath."bootstrap/css/bootstrap.min.css");  
           // $quickForm->addJsFile(QuickFormConfig::$quickFormResourcePath."bootstrap-select/js/bootstrap-select.min.js"); 
           // $quickForm->addCssFile(QuickFormConfig::$quickFormResourcePath."bootstrap-select/css/bootstrap-select.min.css")
            $quickForm->addJsFile(QuickFormConfig::$quickFormResourcePath."select2/js/select2.full.min.js"); 
            $quickForm->addCssFile(QuickFormConfig::$quickFormResourcePath."select2/css/select2.min.css");
              $quickForm->addJsFile(QuickFormConfig::$quickFormResourcePath."awesomplete/awesomplete.min.js"); 
            $quickForm->addCssFile(QuickFormConfig::$quickFormResourcePath."awesomplete/awesomplete.css");
             $quickForm->addJsFile(QuickFormConfig::$quickFormResourcePath."bootstrap-switch/js/bootstrap-switch.min.js"); 
            $quickForm->addCssFile(QuickFormConfig::$quickFormResourcePath."bootstrap-switch/css/bootstrap-switch.min.css");  
            $quickForm->addJsFile(QuickFormConfig::$quickFormResourcePath."sortabletable.js");
            $quickForm->addJsFile(QuickFormConfig::$quickFormResourcePath."quickajax.js");
            $quickForm->addJsFile(QuickFormConfig::$quickFormResourcePath."quickform.js");
            $quickForm->addJsFile(QuickFormConfig::$quickFormResourcePath."echarts.min.js");
            $quickForm->addJsFile(QuickFormConfig::$quickFormResourcePath."echarts-gl.min.js");
            $quickForm->addJsFile(QuickFormConfig::$quickFormResourcePath."datatables/media/js/jquery.dataTables.min.js");  
            $quickForm->addJsFile(QuickFormConfig::$quickFormResourcePath."datatables/media/js/dataTables.bootstrap.min.js");  
            $quickForm->addJsFile(QuickFormConfig::$quickFormResourcePath."datatables/extensions/ColReorder/js/dataTables.colReorder.min.js");   
            $quickForm->addJsFile(QuickFormConfig::$quickFormResourcePath."datatables/extensions/RowReorder/js/dataTables.rowReorder.min.js");  
            $quickForm->addJsFile(QuickFormConfig::$quickFormResourcePath."datatables/extensions/FixedHeader/js/dataTables.fixedHeader.min.js");
           // $quickForm->addJsFile(QuickFormConfig::$quickFormResourcePath."datatables/extensions/Responsive/js/dataTables.responsive.min.js");
           // $quickForm->addJsFile(QuickFormConfig::$quickFormResourcePath."datatables/extensions/Responsive/js/responsive.bootstrap.min.js"); 
            $quickForm->addJsFile(QuickFormConfig::$quickFormResourcePath."datatables/extensions/Buttons/js/dataTables.buttons.min.js"); 
            $quickForm->addJsFile(QuickFormConfig::$quickFormResourcePath."datatables/extensions/Buttons/js/buttons.bootstrap.min.js"); 
            $quickForm->addJsFile(QuickFormConfig::$quickFormResourcePath."datatables/extensions/Buttons/js/buttons.colVis.min.js"); 
            $quickForm->addJsFile(QuickFormConfig::$quickFormResourcePath."datatables/extensions/Buttons/js/buttons.print.min.js"); 
            $quickForm->addJsFile(QuickFormConfig::$quickFormResourcePath."datatables/extensions/Buttons/js/buttons.html5.min.js");   
            //$quickForm->addJsFile(QuickFormConfig::$quickFormResourcePath."vue.min.js");   
           // $quickForm->addJsFile(QuickFormConfig::$quickFormResourcePath."iviewui/iview.min.js");   
            $quickForm->addJsFile(QuickFormConfig::$quickFormResourcePath."ion.sound/js/ion.sound.min.js");   
            $quickForm->addJsFile(QuickFormConfig::$imagePickerPath."image-picker.min.js");
            $quickForm->addJsFile(QuickFormConfig::$chosenPath."chosen.jquery.min.js");
            //$quickForm->addJsFile(QuickFormConfig::$quickFormResourcePath."JsBarcode.all.min.js");
            $quickForm->addJsFile(QuickFormConfig::$quickFormResourcePath."perfect-scrollbar/js/perfect-scrollbar.jquery.min.js");
            $quickForm->addJsFile(QuickFormConfig::$ueditPath."ueditor.config.js",QuickFormConfig::$encode);
            $quickForm->addJsFile(QuickFormConfig::$ueditPath."ueditor.all.js",QuickFormConfig::$encode);
            $quickForm->addJsFile(QuickFormConfig::$quickFormResourcePath."sweetalert2/sweetalert2.min.js");
            $quickForm->addJsFile(QuickFormConfig::$quickFormResourcePath."jquery-ui.multidatespicker.js");
             $quickForm->addCssFile(QuickFormConfig::$quickFormResourcePath."jquery-ui.multidatespicker.css");
            $quickForm->addCssFile(QuickFormConfig::$quickFormResourcePath."sweetalert2/sweetalert2.min.css");
            $quickForm->addCssFile(QuickFormConfig::$quickFormResourcePath."perfect-scrollbar/css/perfect-scrollbar.min.css");
            $quickForm->addCssFile(QuickFormConfig::$chosenPath."chosen.css");
            $quickForm->addCssFile(QuickFormConfig::$imagePickerPath."image-picker.css");
            $quickForm->addCssFile(QuickFormConfig::$quickFormResourcePath."quickform.css");
            // $quickForm->addCssFile(QuickFormConfig::$quickFormResourcePath."iviewui/styles/iview.css");
            $quickForm->addCssFile(QuickFormConfig::$quickFormResourcePath."datatables/media/css/dataTables.bootstrap.min.css");
            $quickForm->addCssFile(QuickFormConfig::$quickFormResourcePath."datatables/extensions/ColReorder/css/colReorder.bootstrap.min.css");   
            $quickForm->addCssFile(QuickFormConfig::$quickFormResourcePath."datatables/extensions/RowReorder/css/rowReorder.bootstrap.min.css");   
            $quickForm->addCssFile(QuickFormConfig::$quickFormResourcePath."datatables/extensions/FixedHeader/css/fixedHeader.bootstrap.min.css");
         //   $quickForm->addCssFile(QuickFormConfig::$quickFormResourcePath."datatables/extensions/Responsive/css/responsive.dataTables.min.css");
            $quickForm->addCssFile(QuickFormConfig::$quickFormResourcePath."datatables/extensions/Buttons/css/buttons.bootstrap.min.css");
            $quickForm->addJsFile(QuickFormConfig::$quickFormResourcePath."jquery.formtowizard.js"); 
            $quickForm->addJsFile(QuickFormConfig::$quickFormResourcePath."daterangepicker/moment.min.js");
            $quickForm->addJsFile(QuickFormConfig::$quickFormResourcePath."daterangepicker/moment-timezone.min.js");
            $quickForm->addJsFile(QuickFormConfig::$quickFormResourcePath."daterangepicker/daterangepicker.js");  
            $quickForm->addCssFile(QuickFormConfig::$quickFormResourcePath."daterangepicker/daterangepicker.css");
            $quickForm->addCssFile(QuickFormConfig::$quickFormResourcePath."lobibox/css/lobibox.min.css");
            $quickForm->addJsFile(QuickFormConfig::$quickFormResourcePath."lobibox/js/lobibox.min.js");
            $quickForm->addCssFile(QuickFormConfig::$quickFormResourcePath."lobipanel/css/lobipanel.min.css");
            $quickForm->addJsFile(QuickFormConfig::$quickFormResourcePath."lobipanel/js/lobipanel.min.js");
            $quickForm->addCssFile(QuickFormConfig::$quickFormResourcePath."buttons.css");  
            $quickForm->addJsFile(QuickFormConfig::$quickFormResourcePath."fontawesome/js/fontawesome-all.min.js");
            $quickForm->addCssFile(QuickFormConfig::$quickFormResourcePath."fancytree/skin-win8/ui.fancytree.min.css");
            $quickForm->addJsFile(QuickFormConfig::$quickFormResourcePath."fancytree/jquery.fancytree-all.min.js");  
            $quickForm->addJsFile(QuickFormConfig::$quickFormResourcePath."js-excel-generator/external/FileSaver.js");  
            $quickForm->addJsFile(QuickFormConfig::$quickFormResourcePath."js-excel-generator/external/jszip.min.js"); 
            $quickForm->addJsFile(QuickFormConfig::$quickFormResourcePath."js-excel-generator/scripts/excel-gen.js");  
           /* $object = new ReflectionObject($quickForm);
            $method = $object->getMethod($initMethod);
            $declaringClass = $method->getDeclaringClass();
            $quickForm->addHidden("qp_qflp",base64_encode($declaringClass->getFilename()));*/
            $quickForm->addHidden("exportFormat",$quickForm->getDefaultExportFormat());
            $quickForm->addHidden("ed_ajaxmethod");
            $quickForm->addHidden("ed_formmark",$quickForm->getFormMark());
            $quickForm->addHidden("ed_colmappingtype");
            $quickForm->addHidden("ed_colmappingkey");
            $quickForm->addHidden("ed_colmappingmainid");
            $quickForm->addHidden("ed_colmappingchoosedvalue");
            $quickForm->addHidden("qp_dataid");
            $quickForm->initResource($src);
            $quickForm->getColDetail();
            if($quickForm->getEditDefaultValueFromDb()&&$quickForm->isAddMode())
            {
                $quickForm->loadEditDefaultValueFromDb();
            }
            if($quickForm->getValidateFromDb())
            {
                $quickForm->loadValidateFromDb();
            }
            $searchMapping = $quickForm->getSearchMapping();
            foreach($searchMapping as $dbname =>$key)
            {
                $value = $src[$key];
                $quickForm->addTransfer($key,$value);
            }
            $this->quickForm = $quickForm;  
            return $quickForm;
        }

        public function getDisplayName($dbname,$must=false,$editform=false)
        {
            $quickForm= $this->quickForm;
            return $quickForm-> getDisplayName($dbname,$must,$editform);
        }
        

        protected function initCols($isExport=false)
        {
            $this->quickForm->setIsExportMode($isExport);
            $quickForm= $this->quickForm;
            $fields = $quickForm->getReportField();
            $key = "reportmode";

            if($isExport)
            {
                 $fields = $quickForm->getExportField();
                 $key = "exportmode";
            }
             if(!$isExport&&$quickForm->isSeq())
            {
                $this->quickForm->addTitle($quickForm->getSeqTitle(), "_quickform_seq_for_datatables","None");
                $this->quickForm->addStructureByDbName("_quickform_seq_for_datatables",true,"getRowNum");
            }

            if((is_array($quickForm->getMainIdCol())||($quickForm->getChooseCheckBoxDbName()!=null&&trim($quickForm->getChooseCheckBoxDbName())!=""))&&($quickForm->isEdit()||$quickForm->isDelete()||$quickForm->isChoose())&&!$isExport&&$quickForm->isShowChoose())
            {   
                $this->quickForm->setIsChoose(true);
                $checkBoxId = $quickForm->getCheckBoxId();
                $this->quickForm->addTitle($quickForm->getCheckAllCheckBox(), $checkBoxId,"None");
                $this->quickForm->addStructureByDbName($checkBoxId,true,$this->quickForm->getChooseCheckBoxMethod());
            }
        
            foreach($fields as $dbname => $showmode)
            { 
                
                $displayname = $this->getDisplayName($dbname);
                $jsordertype = $quickForm->getJsOrderType($dbname);
                $this->quickForm->addTitle($displayname,$dbname,$jsordertype);
                $this->quickForm->addStructureByDbName($dbname,true,$showmode);
            }
            $detailfields = $quickForm->getDetailField();
            $this->quickForm->addJsFile(QuickFormConfig::$quickFormResourcePath."viewData.js");
            if($quickForm->isDetail()&&is_array($detailfields)&&count($detailfields)>0)
            {
                 $this->quickForm->addTitle($this->quickForm->getDetailName(), "_view_data_detail","None");
                 $this->quickForm->addStructureByDbName("_view_data_detail",true,"getViewDetailByMainId");
            }
        
         }
    
         protected function getGroupClause()
         {

              return $this->quickForm->getGroupSql();
         }

         protected function getOrderClause()
         {
              return $this->quickForm->getOrderSql();
         }

        public function getEditMode($dbname,$src,$methodname=null)
        {
             $quickForm= $this->quickForm;
             $fields = $quickForm->getEditField();
             $save = $fields[$dbname]["save"];
             $upload = $fields[$dbname]["upload"];
             if($methodname==null)
             {
                $methodname = $fields[$dbname]["method"];
             }
            return $quickForm->showEditShowMode($methodname,$save,$upload,$dbname,$src);
        }
        
        public function getSearchMode($dbname,$src,$sql=false,$methodname=null,$defaultsearch=null,$showSearchBar=false)
        {
            return $this->quickForm->getSearchMode($dbname,$src,$sql,$methodname,$defaultsearch,$showSearchBar);
        }

        public function getWhereClause($src,$modifySql=false,$searchPrefix=null)
        {
            return $this->quickForm->getWhereSql($src,$modifySql,$searchPrefix);
        } 



        protected function getSql($src)
        {   
             
             $where = " ";
            $finalWhereClause = $this->quickForm->getFinalWhereClause();
            if($finalWhereClause!=null&&trim($finalWhereClause)!="")
            {
                $where = "WHERE 1=1 AND ".$finalWhereClause;
            }
            else
            {
                $where = $this->getWhereClause($src,true);
            }
            if(trim($where)=="WHERE 1=1")
            {
                $where =" ";
            }
            $result = $where.$this->getGroupClause().$this->getHavingClause();
            $result =  $this->quickForm->getSql($src).$result;
            $result = $this->quickForm->modifyOnClause($result);
            return $result;
        }

        protected function getHavingClause()
        {
            return $this->quickForm->getHavingSql();
        }
    
        public function getForm($db,$src,$curpage=1,$pagerows=null,$isExport=false,$blank=false,$orderMethod=null,$searchMethod=null)
        {

            if($this->quickForm->getDb()!=null)
            {
                $db = $this->quickForm->getDb();
            }
            $src = $this->quickForm->editSrc($db,$src);
            $this->quickForm->setDataSrc($src);
            $method = ArrayTools::getValueFromArray($src,'method');

            $debug = ArrayTools::getValueFromArray($src,'debug');
            $exportmode = trim(strtolower(ArrayTools::getValueFromArray($src,'exportmode')));
            $searchSign = intval(ArrayTools::getValueFromArray($src,'searchSign'));
            $this->quickForm->setIsExport($isExport);
            $this->quickForm->deleteFormDataByMainId($db,$src);

            if($isExport)
            {
                $src["searchSign"] = 1;
            } 
           
            if(!$blank)
            {
               
                $blank = $this->quickForm->isBlank();
            } 

            if(is_bool($this->blank))
            {
                
                $blank = $this->blank;
            }

            
           if($blank)
            {   
                    $where = $this->getWhereClause($src);
                    

                    if(trim($where) == "WHERE 1=1" &&  $searchSign!=1)
                    {
                       
                        $blank = true;
                    }
                    else if($searchSign==1)
                    {
                        
                        $blank = false;
                    }
             }
          
            if($pagerows!==null&&trim($pagerows)!="")
            {
                $this->quickForm->setPageRows(intval($pagerows));
            }

            if(StringTools::isStartWith($exportmode,"all"))
            {
               $this->quickForm->setPageRows(0);
            }
            if($this->getReportName()==null||trim($this->getReportName())=="")
            {
                $this->setReportName($this->quickForm->getReportName()); 
            }

            $methodResult = $this->quickForm->execMethod($db,$method,$src);
          
            $this->quickForm->setMethodResult($methodResult);
            $this->quickForm->beforeLoad($db,$src);
            if($orderMethod!=null&&trim($orderMethod)!="")
            {
                $this->quickForm->setOrderMethod($orderMethod);
            }

            if($searchMethod!=null&&trim($searchMethod)!="")
            {
                $this->quickForm->setSearchMethod($searchMethod);
            }
            $orderMethod = $this->quickForm->getOrderMethod();
            $searchMethod =  $this->quickForm->getSearchMethod();
           
            if($orderMethod!=null&&trim($orderMethod)!="")
            {
             
                $orderField = $this->quickForm->getOrderField();
                $orderField = $this->quickForm->$orderMethod($src,$orderField);

                if(is_array($orderField))
                {
                  
                    $this->quickForm->setOrderField($orderField);
                }
            }
           
                if($searchMethod!=null&&trim($searchMethod)!="")
                {
                   $whereClause = $this->quickForm->$searchMethod($src,$this->quickForm->getWhereClause());
                   if($whereClause!=null&&trim($whereClause)!="")
                   {
                        $this->quickForm->setWhereClause($whereClause);
                   }    
                }
                if(is_array($this->where))
                {
                    $whereClause = $this->where["whereClause"];
                    $replace = $this->where["replace"];
                    $relation = $this->where["relation"];
                    if($whereClause!=null&&trim($whereClause)!=""&&!$replace)
                    { 
                            $oriWhereClause = $this->getWhereClause();
                            if($oriWhereClause!=null&&trim($oriWhereClause)!="")
                            {
                                $whereClause = "(".$oriWhereClause.") ".$relation." ".$whereClause;
                            }   
                    }
                    $this->quickForm->setWhereClause($whereClause);
                }
            
           
            if(!$blank||!$this->quickForm->getSearchBar()||$isExport)
            {  
                   
                    $customDataMethod = $this->quickForm->getCustomDataMethod();

                    if($customDataMethod!=null&&trim($customDataMethod)!="")
                    {
                        $this->quickForm->$customDataMethod($db,$src,$this->quickForm->getPageRows(),$curpage);
                        
                    }
                    else
                    {
                        
                        $fullSql = $this->getSql($src); 
                        if($this->quickForm->getDebug())
                        {
                            print_r($src);
                            echo "<br>".$fullSql.$this->getOrderClause()."<br>";
                        }
                       
                    $dataMsg = new DataMsg();
            
                    $this->quickForm->getData($db,$fullSql,$this->getOrderClause(),$this->quickForm->getPageRows(),$curpage);
                        $this->quickForm->getAllLinkData();
                        $this->quickForm->proessingColMapping($db);
                    }
            }
    
            $this->quickForm->afterLoad($db,$src);
            $this->quickForm->filterResult($db,$src);
            $this->quickForm->runRelationDataMethod($db,$src);
            $this->initCols($isExport);
            if($isExport&&trim($isExport)!="csv")
            {

                 $exportMethod = $this->quickForm->getExportMethod($exportmode,$src["exportFormat"]);
                 $this->quickForm->$exportMethod($this->quickForm->getExportFileName($src["exportFormat"]),null,$this->quickForm->isExportWithTitle(),$src,$methodResult);
            }

          
            return  $this->quickForm;
        }

        public function getSearchBarHtml($src=null,$showSearchBar=false,$showCustomCol=true)
        {
            return $this->quickForm->getSearchBarHtml($src,$showSearchBar,$showCustomCol);
        }

}