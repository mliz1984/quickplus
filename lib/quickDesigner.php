<?php 
namespace Quickplus\Lib;
use Quickplus\Lib\quickForm;
use Quickplus\Lib\Tools\DbTools as DbTools;
use Quickplus\Lib\Tools\FileTools as FileTools;
use Quickplus\Lib\Tools\UrlTools as UrlTools;
use Quickplus\Lib\Tools\StringTools as StringTools;
use Quickplus\Lib\Tools\HtmlElement as HtmlElement;
use Quickplus\Lib\QuickExcelReader;
use Quickplus\Lib\QuickHtmlDesignerExtend;
    require_once(dirname(__FILE__)."/mpdf/mpdf.php"); 
    class QuickDesigner extends QuickForm
    {
    
        protected $templateFileName = null;
        protected $linkSql = Array();
        protected $linkData = Array();
        protected $mainData = Array();
        protected $mainDataTitle = "";
        protected $templateKey = "id";
        protected $templateFile = null;
        protected $templateData = null;
        protected $mainField = Array();
        protected $params = Array();
        protected $templatePath = defaultTemplatePath;
        protected $customZoomSetting = Array();
        protected $cellZoomSetting = Array();
        protected $pageZoomSetting = null;
        protected $designMode = true;
        protected $pdfFileName ="export.pdf";
        protected $tableId = "mainTable";
    
        public function setTableId($tableId)
        {
          $this->tableId = $tableId;
        }

        public function getTableId()
        {
          return $this->tableId;
        }
         public function setPdfFileName($pdfFileName)
         {
            $this->pdfFileName = $pdfFileName;
         }
         public function setDesignMode($designMode)
         {
            $this->designMode = $designMode;
         }
        public function setTemplatePath($templatePath)
        {
           $this->templatePath = $templatePath;
        }

        public function setPageZoomSetting($rate)
        {
            $this->pageZoomSetting = $rate;
        }
        public function getCellZoomSetting()
        {
           return $cellZoomSetting;
        }
        public function setCustomZoomSettingByMethod($method,$zoom)
        {
           $zoom = intval($zoom);
           $oldZoom = intval( $this->customZoomSetting[$method]);
           if($zoom>$oldZoom&&$zoom>0)
           {
              $this->customZoomSetting[$method] = $zoom;
           }
        }

        public function addParam($key)
        {
           $this->params[$key] = $key;
        }


        public function setTemplateFile($templateFile)
        {
           $this->templateFile = $templateFile;
        }
        public function setTemplateData($templateData)
        {
           $this->templateData = $templateData;
        }
        public function setTemplateKey($templateKey)
        {
           $this->$templateKey = $templateKey;
        }
        public function setMainDataTitle($mainDataTitle)
        {
            $this->mainDataTitle = $mainDataTitle;
        }
        public function setLinkSql($id,$title,$sql)
        {
            $colInfo = DbTools::getColNames($sql);
            $this->linkSql[$id] = Array("title"=>$title,"sql"=>$sql,"colInfo"=>$colInfo);
        }   

        public function setLinkFieldHtml($id,$dbname,$htmlMethod)
        {
          $this->linkSql[$id]["field"][$dbname]["htmlMethod"] = $htmlMethod;
        }

        public function setLinkFieldParams($id,$dbname,$params)
        {
          $this->linkSql[$id]["field"][$dbname]["params"] = $params;
        }

        public function setMainFieldHtml($dbname,$htmlMethod)
        {
          $this->mainField[$dbname]["htmlMethod"] = $htmlMethod;
        }

        public function setMainFieldParams($dbname,$params)
        {
             $this->mainField[$dbname]["params"] = $params;
        }


        public function addLinkField($id,$dbname,$title,$method=null,$mappingdbname=null)
        {
            $this->linkSql[$id]["field"][$dbname]["title"] = $title ;
            $this->linkSql[$id]["field"][$dbname]["method"] = $method ;
            $this->linkSql[$id]["field"][$dbname]["htmlMethod"] = "getEditInputButtonHtml";
            $this->linkSql[$id]["field"][$dbname]["mappingdbname"] = $mappingdbname;
            $this->linkSql[$id]["field"][$dbname]["params"] = Array();
        }

         public function addMainField($dbname,$title,$method=null,$mappingdbname=null)
        {
            $this->mainField[$dbname]["title"] = $title ;
            $this->mainField[$dbname]["method"] = $method ;
            $this->mainField[$dbname]["htmlMethod"] = "getEditInputButtonHtml";
            $this->mainField[$dbname]["mappingdbname"] = $mappingdbname;
            $this->mainField[$dbname]["params"] = Array();

        }

        public function saveTemplateBase($db,$src=null)
        {
            $templateId  = $src[$this->templateKey];
            $templateData = CommonTools::getDataArray($src,"cell_hidden_",false);
            return $this->saveTemplate($db,$templateId,$templateData,$src);
        }

        public function saveTemplate($db,$templateid,$templateData,$src)
        {
            return false;
        }
      
      public function setTemplateFileName($filename,$isAbsolutePath=true)
      {
        if(!$isAbsolutePath)
        {
          $filename = FileTools::getRealPath($filename);
        }
        $this->templateFileName = $filename;
      }

      public function getTemplateHtml($headerHtml,$footerHtml)
      {
        $result =  "Can't find Template File:".$this->templateFileName;
        $qer = new QuickExcelReader();
        $qer->setTableId($this->getTableId());
        $qer->setCustomHeaderHtml($headerHtml);
        $qer->setCustomFooterHtml($footerHtml);
        if($qer->loadFile($this->templateFileName))
        {
          $qde =new QuickHtmlDesignerExtend();
          $qde->setDesignMode($this->designMode);
          $qer->setHtmlWriterExtend($qde);
          $result =  $qer->getHtmlString();
      }
      return $result;
      }

      public function initEdit($src=null)
      {
        $this->setMethod("saveTemplate", "saveTemplateBase");
        $this->setMethodSuccess("saveTemplate","Save successfuly", UrlTools::getFullUrl()."?".$this->templateKey."=".$src[$this->templateKey]);
        $this->setEditPrefix("");
        $this->addEditHidden($this->templateKey,$src[$this->templateKey]);
        $this->addField("template","Template Content:");
        $this->setEditorWidth("template",650);
        $this->setEditorHeight("template",200);
        $this->setEditFieldType("template", "editorSearchShowMode");
        }

      
        protected function getMainData()
        {
            $db = $this->getDb();
            $dataMsg = new DataMsg();
            $dataMsg->findBySql($db,$this->sql);
            $this->mainData = $dataMsg->getDataArray(); 

        }

      
        
        public function initMainData($src=null)
        {
             
        }
        public function initLinkData($src=null)
        {
    
        }
        protected  function getLinkData()
        { 
             foreach($this->linkSql as $id=>$linkInfo)
             {
                $sql = $linkInfo["sql"];
                $dataMsg = new DataMsg();
                $dataMsg->findBySql($this->getDb(),$sql);
                $this->linkData[$id] = $dataMsg->getDataArray();

             }
        }
        public function getCellValue($value,$dataNum,$postArray,$isExtendCol=false)
        {
          return $value;  
        }
        public function getCellDataString($row,$col,$postArray,$extendNumber=null)
        {

            $isMain = $postArray["isMain"];
            $dataid = $postArray["dataid"];
            $method = $postArray["method"];
            if($method==null||trim($method)=="")
            {
              $method = "getCellValue";
            }
            $zoom = $this->customZoomSetting[$method];
            if($zoom!=null&&trim($zoom)!=""&&intval(trim($zoom))>0)
            {
               $zoom = intval($zoom);
               $this->cellZoomSetting[$row.$col] = $zoom;
            }
            $col = $postArray["col"];
            $dataNum = intval($postArray["dataNum"])-1;
            $extendCol = false;
            if($postArray["extendType"]!="NO")
            {
               $extendCol = true;
            }
            
            $data = $this->mainData;
            if($isMain=="false")
            {
                $data = $this->linkData[$dataid];
            }
            $result  = "";
            if($extendNumber!=null)
            { 
              if($extendCol&&($dataNum+$extendNumber)<=count($data))
              {
                $dataNum = $dataNum+$extendNumber;
                $result  = $this->$method($data[$dataNum][$col],$dataNum,$postArray,true);
              }
            } 
            else
            {
               $result  = $this->$method($data[$dataNum][$col],$dataNum,$postArray);
            }
            $result = StringTools::escapeJsonString($result);
            return $result;
        }

        public function getCellString($col,$row,$string,$extend=false,$extendNumber=null)
        {
                $postion = $col.$row;
                $string = StringTools::conv(htmlspecialchars_decode($string),QuickFormConfig::$encode);   
                $array = explode("qdds-}",$string);
                $result = "";
                $extendNum = 0;
                $isExtend = false;
                foreach($array as $a)
                {
                    $have =  strstr($a,"{-qdds");
                    if($have)
                    {
                        $pos =  strpos($a,"{-qdds");
                        $result .=substr($a,0,$pos);
                        $params = substr($a,$pos+6);
                        $paramsArray = explode("&", $params);
                        $postArray = Array();
                        foreach($paramsArray as $p)
                        {
                            $paramDetail = explode("=",$p);
                            $arrayCount = count($paramDetail);
                            if($arrayCount>1)
                            {
                                $paramName = trim($paramDetail[0]);
                                $paramValue = trim($paramDetail[1]);
                                $postArray[$paramName] = $paramValue; 
                            }
                        }
                        $dataid = $postArray["dataid"];
                        $dataNum = $postArray["dataNum"];
                        $isMain = $postArray["isMain"];
                        if($dataNum=="max")
                        {
                           if($isMain=="false")
                           { 
                              $postArray["dataNum"] = count($this->linkData[$dataid]);
                           }
                           else
                           { 
                              $postArray["dataNum"] = count($this->mainData);
                           }
                        }
                        if(!$extend&&$postArray["extendType"]!="NO")
                        {
                             $dataNum = intval($postArray["dataNum"])-1;
                             $data = $this->mainData;
                             if($isMain=="false")
                             {
                                $data = $this->linkData[$dataid];
                             }
                             $totalNum = count($data);
                             $diff = $totalNum - $dataNum;
                             if($diff>0)
                             {
                                $isExtend = true;
                                if($diff>$extendNum)
                                {
                                   $extendNum = $diff;
                                }
                             }
                        }
                        $result.=$this->getCellDataString($row,$col,$postArray,$extendNumber);
                    }
                    else
                    {
                        $result .=$a;
                    }
                }
                $array =  Array(strtolower($postion) => StringTools::conv($result));
                if($isExtend&&!$extend)
                {

                    for($i=1;$i<$extendNum;$i++)
                    {
                         
                         if($postArray["extendType"]=="DOWN")
                         {
                           $extArr = $this-> getCellString($col,$row+$i,$string,true,$i);
                            $array = array_merge($array,$extArr);
                         }
                         else
                         {
                            $extArr = $this-> getCellString($col,$row,$string,true,$i);
                            $tmp = $array[strtolower($postion)] . $extArr[strtolower($postion)];
                            $tmp = str_replace("<p>","",$tmp);
                            $tmp = str_replace("</p>","<br>",$tmp);
                            if($i=$extendNum-1)
                            {
                              $tmp = "<p>".$tmp."</p>";
                            }
                            $array[strtolower($postion)]  = $tmp;

                         } 
                         
                    }
                }
                foreach($array as $i=>$v)
                {
                  if(!$this->designMode)
                  {
                      $v = str_replace("'","\'", $v);
                  }
                  else
                  {
                    $v = str_replace('"',"'", $v);
                  }                 

                  $array[$i] = $v;
                }
                return $array;
        }

        protected function getDataSelectorHtml($isMainSql,$dataId,$dataCount)
        {
            $objId = "";
            if($isMainSql)
            {
                $objId = "quickdesginerMainsql";
            }
            else
            {
                $objId = "quickdesginer_".$dataId;
            }
            $html = "";
            if($dataCount>1)
            {
                $html ="<br>Total:".$dataCount." datas<br/>select No.<SELECT id='". $objId."_dataNum'>";
                for($i=1;$i<=$dataCount;$i++)
                {
                    $html.="<option value='".$i."'>".$i."</option>";
                }
                  $html.="<option value='max'>Max</option>";  
                $html.="</SELECT><br>Extend:<SELECT id='". $objId."_extendType'><option value='NO'>None</option><option value='DOWN'>Vertical</option><option value='INLINE'>Inline</option></SELECT>";
            }
            else
            {
                $html = "<input id='". $objId."_dataNum' type='hidden' value='1' /> <input id='". $objId."_extendType' type='hidden' value='NO' /> ";
            }
            return $html;
        } 
      protected function getEditInputButtonHtml($dataid,$isMainSql,$dbname,$id,$fieldInfo,$value)
        {
            $id = $dbname."_editor_".$id."_button";
            $html = new HtmlElement($id,$id);
            $html->setParam("dataid",$dataid);
            $isMain = "false";
            $data = null;
            $objId = "";
            $echo = $fieldInfo["title"];
            $method = $fieldInfo["method"];
            $mappingdbname = $fieldInfo["mappingdbname"];
            if($mappingdbname!=null&&trim($mappingdbname)!="")
            {
              $value = $mappingdbname;
            }
            if($method==null||trim($method)=="")
            {
              $method = "getCellValue";
            }
            if($isMainSql)
            {
                $objId = "quickdesginerMainsql_";
                $isMain = "true";
                $data = $this->mainData;
            }
            else
            {
                $objId = "quickdesginer_".$dataid."_";
                $data = $this->linkData[$id];
            }
            $dataCount = 0;

            $html->setParam("isMain",$isMain);
          //  $js = "var ".$objId."dataNum = $('#".$objId."dataNum').val();var ".$objId."extendType = $('#".$objId."extendType').val();"; 
             $js = "var ".$objId."value = '{-qdds col=".$value."&dataid=".$dataid."&dataNum='+ $('#".$objId."dataNum').val() +'&isMain=".$isMain."&extendType='+ $('#".$objId."extendType').val() +'&method=".trim($method);
             $params = $fieldInfo["params"];
             if(is_array($params)&&count($params)>0)
             {
                foreach($params as $p => $pid)
                {
                   $js.= "&".$p."=' + $('#".$pid."').val() + ";
                }
                $js .="'";
             }
             $js .= " qdds-}';";
            $js .= $dbname."_editor.execCommand('inserthtml',".$objId."value);";
            $html->setFunction("onClick",$js);
            return $html->getButton($echo);
        }

      public function loadTemplate($db,$templateid,$src)
      {
      }
      
      public function initParams($sec)
      {

      }
      public function savePdf($db,$src,$filename,$loadData=true)
      {
          $this->getPdf($db,$src,$loadData,$filename,"F");
      }
      public function viewPdf($db,$src,$loadData=true)
      {
        $this->getPdf($db,$src,$loadData);
      }
      public function exportPdf($db,$src,$filename=null,$loadData=true)
      {
          if($filename==null||trim($filename)=="")
          {
             $filename = $this->pdfFileName;
          }
          $this->getPdf($db,$src,$loadData,$filename,"D");
      }
      protected  function getPdf($db,$src,$loadData=true,$fileName='',$dest='')
      {
        ob_end_clean();
        $this->setDesignMode(false);
        $html = $this->getHtmlString($db,$src,$loadData,false,false);
        $mpdf = new Mpdf();
        $mpdf->WriteHTML($html);
        $mpdf->Output($fileName,$dest);
      }

      public function getHtmlString($db,$src,$loadData,$loadJs,$pageZoom=false,$footerHtml="")
      {
        
        $this->db = $db;
        $this->initParams($src);
        $id = $src[$this->templateKey];
        $this->loadTemplate($this->getDb(),$id,$src);
        $filename =FileTools::connectPath($this->templatePath,$this->templateFile);
        $this->setTemplateFileName($filename,false);
        if($loadData)
        {
              $this->initMainData($src);
              $this->getMainData();  
              $this->initLinkData($src);
              $this->getLinkData(); 
        }
        $headerHtml = "";
        if($loadJs&&$this->designMode)
        {
          $headerHtml = '<script type="text/javascript" language="javascript" src="'.QuickFormConfig::$jquery.'" charest="utf-8"></script>';
        }
        $js = $this->getInitJs($src);
        $zoomJs = "";
        $style = "";
        if($pageZoom&&$this->pageZoomSetting!=null&&floatval($this->pageZoomSetting)>0)
        { 

           $style .="#".$this->getTableId()." {zoom:".floatval($this->pageZoomSetting).";}"; 
            foreach($this->cellZoomSetting as $p=>$z)
             {
               $style .="#cell_".$p." {zoom:".floatval($z)/floatval($this->pageZoomSetting).";}"; 
             }   
        }
        if($style!=null&&trim($style)!="")
        {
          $headerHtml.= "<style>".$style."</style>";

        }

        
        return $this->modifyHtml($this->getTemplateHtml($headerHtml,$js.$footerHtml),$pageZoom);
      }
      public function initLayout($src=null)
      {
        $this->initParams($src);
        $id = $src[$this->templateKey];
        $this->loadTemplate($this->getDb(),$id,$src);
        $filename =FileTools::connectPath($this->templatePath,$this->templateFile);
        $this->setTemplateFileName($filename,false);
        $this->initMainData($src);
              $this->getMainData();  
              $this->initLinkData($src);
              $this->getLinkData(); 
          $this->setColByHtml("designer","template",$this->getTemplateHtml("",$this->getInitJs($src)),null,9);
          $this->setColBySub("designer","designerform",3);
          $this->setSubByHtml("designerform","position","position","<b>Current Position:</b><SPAN id='position'></SPAN><input type='hidden' id='positionColValue' value=''/><input type='hidden' id='positionRowValue' value=''/>");
          $this->setSubByEditField("designerform", "template","template");
              
            
              $html = "";
            
              if(is_array($this->mainData)&&count($this->mainData)>0)
              { 

                  $dataCount = count($this->mainData);
                  foreach($this->mainField as $dbname=>$fieldInfo)
                  {
                     $htmlMethod = $fieldInfo["htmlMethod"];
                     if($dbname!=null&&trim($dbname)!="")
                     {
                        $html.=$this->$htmlMethod("mainData",true,"template",$dbname,$fieldInfo,$dbname);
                     }
                    
                  }
                  if($this->mainDataTitle!=null&&trim($this->mainDataTitle)!="")
                  {
                        $html = $this->mainDataTitle.":".$html;
                  }
                 $html.=$this->getDataSelectorHtml(true,"mainData",$dataCount);
                 $this->setSubByHtml("designerform","quickdesginerMainsql_","button", $html);

             }
             foreach($this->linkSql as $id => $linkInfo)
             {

                if(is_array($this->linkData[$id])&&count($this->linkData[$id])>0)
                {
                   
                    if(isset($linkInfo["field"])&&count($linkInfo["field"])>0)
                    {
                         $dataCount = count($this->linkData[$id]);
                        $html = "";
                        foreach($linkInfo["field"] as $dbname => $fieldInfo)
                        {
                            $htmlMethod = $fieldInfo["htmlMethod"];
                            if($dbname!=null&&trim($dbname)!="")
                            {
                              $html.=$this->$htmlMethod($id,false,"template",$dbname,$fieldInfo,$dbname);
                            }
                        }
                        if($linkInfo["title"]!=null&&trim($linkInfo["title"])!="")
                        {
                            $html=$linkInfo["title"].":".$html;
                        }
                        $html.=$this->getDataSelectorHtml(false,$id,$dataCount);
                        $this->setSubByHtml("designerform","quickdesginer_".$id,"button", $html);
                    }
                }
            }
            $applyButton = new HtmlElement();
            $js = "var oldlinkcell = $('#cell_'+$('#positionColValue').val()+$('#positionRowValue').val()).attr('linkcell');var cellArray = oldlinkcell.split(',');$.each(cellArray,function(i,v){\$('#cell_'+v).html('');});$('#cell_hidden_'+$('#positionColValue').val()+$('#positionRowValue').val()).val(template_editor.getContent());$.post('".QuickFormConfig::$quickFormMethodPath."quickDesignerAjaxValue.php',{value:template_editor.getContent(),row:$('#positionRowValue').val(),col:$('#positionColValue').val(),classname:'".$this->getFormMark()."'";
            foreach($this->params as $k => $v)
              { 
              $js.=",".$k.":'".$src[$k]."'";
            }
            $js.="}, function( data ){ data = eval('('+$.trim(data)+')');var linkcell='';$.each(data.stringArray,function(index, value)".' {$(\'#cell_\'+index).html(value);linkcell = linkcell+\',\'+index;});$(\'#cell_\'+$(\'#positionColValue\').val()+$(\'#positionRowValue\').val()).attr(\'linkcell\',linkcell.substr(1));});';
            $applyButton->setFunction("onClick",$js);
            $this->setSubByHtml("designerform","quickdesginer_apply_botton","quickdesginer_apply_botton", $applyButton->getButton("Apply"));
            $this->setSubByHtml("designerform","buttonBar","buttonBar", $this->getSubmitButton("Save All Data","saveTemplate"));
            
      }

    protected function modifyHtml($html)
    {
        if(!$this->designMode)
        {
           $replaceArray = Array();
           if(is_array($this->templateData))
            {
                 foreach($this->templateData as $p =>$v)
                 {
                     $cellInfo = StringTools::parseExcelCellString($p);
                     $cellRow = $cellInfo["row"];
                     $cellCol = $cellInfo["col"];
                     $array = $this->getCellString($cellCol,$cellRow,$v);
                     foreach($array as $ap=>$av)
                     {
                       $replaceArray[$ap] = $av;
                     }
                 }
            }

            foreach( $replaceArray as $rp =>$rv)
            {
                  $rv = stripslashes($rv);
                 //$rv = str_replace("\'","", $rv);
                    
              $str = 'id="cell_'.$rp.'" >';
              $find = StringTools::cutString($html,$str,"</td>");          
              $rv = StringTools::conv($rv,QuickFormConfig::$encode);  
              $html = str_replace($find, $str.$rv."</td>", $html);
            }
         }
         return $html;
    }
    
    protected function getInitJs($src)
    {
      $js = "";
      if($this->designMode&&is_array($this->templateData))
            {
               $js ="<script>";
               foreach($this->templateData as $p =>$v)
               {
                
                  $cellInfo = StringTools::parseExcelCellString($p);          
                  $cellRow = $cellInfo["row"];
                  $cellCol = $cellInfo["col"];
                  //$this->getCellString($cellCol,$cellRow,$v);
                   $js.="$('#cell_hidden_".$p."').val('".$v."');";
                  if($this->designMode)
                  {
                    $js .= "$.post('".QuickFormConfig::$quickFormMethodPath."quickDesignerAjaxValue.php',{value:'".$v."',row:$('#cell_".$p."').attr('rowval'),col:$('#cell_".$p."').attr('colval'),classname:'".$this->getFormMark()."'";
                     foreach($this->params as $k => $v)
                    { 
                       $js.=",".$k.":'".$src[$k]."'";
                    }
                     $js .="}, function( data ){ data = eval('('+$.trim(data)+')');var linkcell='';$('#cell_hidden_'+data.position).val(data.value);$.each(data.stringArray,function(index, value)".' {$(\'#cell_\'+index).html(value);linkcell = linkcell+\',\'+index;});$(\'#cell_'.$p.'\').attr(\'linkcell\',linkcell.substr(1));});';
                    }
                
               }
               $js.="</script>";
            }

      return $js;
    }

    }
?>