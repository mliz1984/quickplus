<?php
set_time_limit(0);
     require_once(dirname(__FILE__) . "/DataMsg.php");
     require_once(dirname(__FILE__)."/quickChart.php");
     require_once(dirname(__FILE__)."/quickExcel.php");

/*


 * Created on Oct 18, 2013


 * author Mathieu


 * Email:mathieu@mliz.cn


 */ 
class report extends QuickChart{
     protected $title;
     protected $titleInfo;
     protected $titleOrder;
     protected $structure;
     protected $result; 
     protected $totalcount;
     protected $pagerows = 20;
     protected $curpage;
     protected $totalpages;
     protected $execSql;
     protected $reportName;
     protected $extendTitleMap;
     protected $extendDataMap;
     protected $cellAttrMap;
     protected $presearch = "searchField";
     protected $sqlBuilder = null;  
     protected $countCol = null;
     protected $db = null;
     protected $oriCsv = false;
     protected $defaultValue = array();
     protected $isExportMainData = true;
         public function setIsExportMainData($isExportMainData)
         {
            $this->isExportMainData = $isExportMainData;
         }
         public function getTitle()
         {
            return $this->title;
         }
          public function getStructure()
         {
            return $this->structure;
         }
         public function setDefaultValue($dbname,$defaultValue)
         {
            $this->defaultValue[$dbname] = $defaultValue;

         }    
         
         public function setOriCsv($oriCsv)
         {
            $this->oriCsv = $oriCsv;
         }

         public function getOriCsv()
         {
            return $this->oriCsv;
         }

         public function getDb()
         {
             return $this->db;
         }

         public function setDb($db)
         {
             $this->db = $db;
         }

         public function getCountCol()
         {
             return $this->countCol;
         }

         public function setCountCol($countCol)
         {
            $this->countCol = $countCol;
         }

         protected function setSqlBuilder($sqlBuilder)
         {
             $this->sqlBuilder = $sqlBuilder;
         }

         public function getSqlBuilder()
         {
            return $this->sqlBuilder;
         } 

         public function getDollar($row,$dbname,$export)
         {
            return "$".number_format(round($this->getValueByDbName($row,$dbname,false),2),2,".","");
         }
        
         public function getUpperString($row,$dbname,$export)
         {
            return strtoupper($this->getValueByDbName($row,$dbname,false));
         }

         public function getLowerString($row,$dbname,$export)
         {
            return strtolower($this->getValueByDbName($row,$dbname,false));
         }
         

         public function setCellAttrByDbName($row,$dbname,$key,$value)
         {
            if($this->cellAttrMap==null)
            {
                $this->cellAttrMap = array();
            }
            $this->cellAttrMap[$row][$dbname][$key]= $value;

         }

         public function setCellAttrByOrder($row,$order,$key,$value)
         {
            $dbname =  $this->getDbNameByOrder($order);
            $this->cellAttrMap[$row][$dbname][$key]= $value;
         }

         public function setCellAttrByName($row,$name,$key,$value)
         {
            $dbname =  $this->getDbNameByName($name);
            $this->cellAttrMap[$row][$dbname][$key]= $value;
         }

   
         public function getCellAttrByDbName($row,$dbname)
         {
            $result = "";
            if(is_array($this->cellAttrMap[$row][$dbname]))
            {
                foreach($this->cellAttrMap[$row][$dbname] as $key=>$value)
                {
                    $result .= " ".$key .'="'.$value.'"';
                }
            }
            return $result;
         }
        
        public function getCellAttrByOrder($row,$order)
        {
            $dbname =   $this->getDbNameByOrder($order);
            return  $this->getCellAttrByDbName($row,$dbname);
        }

        public function getCellAttrByName($row,$name)
        {
            $dbname =  $this->getDbNameByName($name); 
            return  $this->getCellAttrByDbName($row,$dbname);
        }

         public function setExtendTitleByStr($csvname,$string,$spilt=",")
         {
              $this->setExtendTitle($csvname,explode($spilt,$string));
         }
     
         public function setExtendTitle($csvname,$array)
         {
             if(!is_array($this->extendTitleMap))
             {
                $this->extendTitleMap = Array();
             }
             $this->extendTitleMap[$csvname] = $array;
         }
         
         public function getExtendTitle($csvname)
         {
             $result = null;
             if(is_array($this->extendTitleMap[$csvname]))
             {
                 $result =  $this->extendTitleMap[$csvname];
             }
             return $result;
         }
         
         public function getCsvData($row,$csvname,$string)
         {
             $title = $this->getExtendTitle($csvname);
            
             if(!is_array($this->extendDataMap))
             {
                 $this->extendDataMap = Array();
             }
             if(is_array($title)&&$string!=null&&trim($string)!="")
             {
                 $csvData = str_getcsv($string);
                 $count=  count($title);
                 $t = count($csvData);
                 if($count>$t)
                 {
                     $count = $t;
                 }
                 $result = Array();     
                 for($i=0;$i<$count;$i++)
                 {
                     $key = $title[$i];
                     $value = $csvData[$i];
                     $result[$key] = $value;
                 }
                 $this->extendDataMap[$row][$csvname] = $result;
             }
         }
         
         public function getJsonData($row,$csvname,$string)
         {
             if(!is_array($this->extendDataMap))
             {
                 $this->extendDataMap = Array();
             }
             $result = Array();
             if($string!=null&&trim($string)!="")
             {
                $result  = json_decode($string);
             }
             $this->extendDataMap[$row][$csvname] = $result;
         }
          public function getJsonValueByString($row,$jsonname,$jsoncol,$string)
          {
             $result = "";
             if(!is_array($this->extendDataMap[$row][$jsonname]))
             {
                 $this->getJsonData($row,$csvname,$string);
             }
             $dataArray = $this->extendDataMap[$row][$jsonname];
             if(is_array($dataArray))
             {
                 $result = $dataArray[$csvcol];
             }
             return $result;
         }
         public function getCsvValueByString($row,$csvname,$csvcol,$string)
         {
             $result = "";
             if(!is_array($this->extendDataMap[$row][$csvname]))
             {
                 $this->getCsvData($row,$csvname,$string);
             }
             $dataArray = $this->extendDataMap[$row][$csvname];
             if(is_array($dataArray))
             {
                 $result = $dataArray[$csvcol];
             }
             return $result;
         }
     
         public function setReportName($reportName)
         {
             $this->reportName =$reportName;
         }    
         
         public function getReportName()
         {
             return $this->reportName;
         }
         
         public function setExecSql($execSql)
         {
             $this->execSql = $execSql;
         }
         
         public function getExecSql()
         {
             return   $this->execSql;
         }
    
    public function getTotalCount()
    {
        return $this->totalcount;
    }

    public function setPageRows($pageRows=20)
    {
        $pageRows = intval($pageRows);
        if($pageRows<=0)
        {
            $pageRows = 0;
        }
    
        $this->pagerows = $pageRows;
    }
    
    public function getPageRows()
    {
        $pagerows = intval($this->pagerows);
         if($pagerows<=0)
         {
            $pagerows = 0;
         }

         return $pagerows;
    }
    
    public function getTotalPages()
    {
         return $this->totalpages;
    }
    
    public function setCurPage($curPage=1)
    {
        $curpage = intval($curPage);
        if($curpage<=1)
        {
            $curpage = 1;
        }
        
        $this->curpage = $curPage;
    }

    public function getCurPage()
    {
         $curpage = intval($this->curpage);
         if($curpage<=1)
         {
            $curpage = 1;
         }
         return $curpage;
    }
     public function createColCsv()
     {
         $str ="";
         for($j=0;$j<count($this->titleOrder);$j++)
         {
              $title = $this->getTitleByOrder($j);
                 if($title["isexport"])
                 {
                      $value = $this->titleOrder[$i];
                      $value = str_replace("\"","\"\"",$value);  
                      $str.= ",\"".$value."\"";
                       for($i=0;$i<$this->getResultSize();$i++)
                       {
                             $value = $this->titleOrder[$i];
                             $value = str_replace("\"","\"\"",$value);  
                             $str.= ",\"".$value."\"";
                       }
                     $str .="\n";
                 }
         }
         return $str;
     }
     
     public function createTitleCsv($getArray= false)
     {
        $titleStr = "";
        $array = Array();
        for($i=0;$i<count($this->titleOrder);$i++)
        {
              $title = $this->getTitleByOrder($i);

              if($title["isexport"])
              {
                $value = $this->titleOrder[$i];

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
              
        }

        $titleStr = substr($titleStr, 1)."\n";
        if($getArray)
        {
             $titleStr = $array;
        }
        return $titleStr;
     }

    public function createExportObj($withTitle=true)
    {
        $mapping = Array();
        $titleData = Array();
        $quickExcel = new QuickExcel();
        $w = -1;
        for($i=0;$i<count($this->titleOrder);$i++)
        {
            $title = $this->getTitleByOrder($i);
            if($title["isexport"])
            {
                $w ++;
                $dbname = $this->getDbNameByOrder($i);
                $colMark = $quickExcel->getColMark($w);

                $mapping[$colMark] = $dbname;
                $titleData[$dbname] =  $this->titleOrder[$i];
            } 
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
                $d[$dbname] =   $this->getValueByDbName($i,$dbname,true,true);   
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
            $quickExcel->setCellDataFromArray($exportData,$mapping);
        }
        $quickExcel = $this->customExport($quickExcel,$mapping,$data,$withTitle,$titleData);
    

        return $quickExcel->getExcelData();
    }

    public function customExport($quickExcel,$mapping,$exportData,$withTitle,$titleData)
    {
        return $quickExcel;
    }

    public function exportPdf($fileName,$exportObj=null,$withTitle=true) 
     {
          header('Content-Type: application/pdf;charset=utf-8');
          header('Content-Disposition: attachment;filename="'.$fileName.'"');  
          header('Cache-Control: max-age=0');
          ob_end_clean();
          if($exportObj==null)
          {
            $exportObj = $this->createExportObj($withTitle);
          } 
          if(QuickFormConfig::$quickPdfRenderer!=null&&trim(QuickFormConfig::$quickPdfRenderer)!="")
          {
                $libpath = FileTools::getRealPath(QuickFormConfig::$quickPdfRendererPath);
                
                if(is_dir($libpath))
                {
                     PHPExcel_Settings::setPdfRenderer(QuickFormConfig::$quickPdfRenderer,$libpath); 
                }
          }
          $objWriter = PHPExcel_IOFactory::createWriter($exportObj, 'PDF');  
          $objWriter->SetFont('arialunicid0-chinese-simplified');
          $objWriter->save('php://output');       
          die();
     }

     public function exportXlsx($fileName,$exportObj=null,$withTitle=true) 
     {
          header('Content-Type: application/vnd.ms-excel;charset=utf-8');  
          header('Content-Disposition: attachment;filename="'.$fileName.'"');  
          header('Cache-Control: max-age=0');
          ob_end_clean();
          if($exportObj==null)
          {
            $exportObj = $this->createExportObj($withTitle);
          } 
          $objWriter = PHPExcel_IOFactory::createWriter($exportObj, 'Excel2007');  
          $objWriter->save('php://output');       
          die();
     }


     public function exportXls($fileName,$exportObj=null,$withTitle=true) 
     {
          header('Content-Type: application/vnd.ms-excel;charset=utf-8');  
          header('Content-Disposition: attachment;filename="'.$fileName.'"');  
          header('Cache-Control: max-age=0');
          ob_end_clean();
          if($exportObj==null)
          {
            $exportObj = $this->createExportObj($withTitle);
          } 
          $objWriter = PHPExcel_IOFactory::createWriter($exportObj, 'Excel5');  
          $objWriter->save('php://output');       
           die();
     }

     public function getCsvString($withTitle=true)
     {
        return $this->createCsv($withTitle);
     }

     public function createCsv($withTitle=true)
     {
        if($withTitle)
        {
            $str.= $this->createTitleCsv();   
        }
        $titleinfo = $this->getTitleInfo();
        
         for($i=0;$i<$this->getResultSize();$i++)
         {
             $dataStr = "";
             foreach($titleinfo as $dbname =>$title)
             {

                 if($title["isexport"])
                 {
                   
                    $value = $this->getValueByDbName($i,$dbname,true,true);
                
                    $value = str_replace("\"","\"\"",$value); 
                    if($this->oriCsv)
                    {
                           $dataStr.= ",".$value;
                    }
                    else
                    {
                        $dataStr.= ",\"".$value."\"";
                    }
                 }
             } 
             $str.= substr($dataStr, 1)."\n";
         }
         return $str;
     }

     
     public function loadCsv($fileName)
     {
         if(!file_exists($fileName))
         {
             return false;
         } 
         $handle = fopen($fileName, 'r'); 
         $result = $this->inputCsv($handle); 
          $row = count($result); 
          $this->result = array();
          if($row<2)
          {
              return false;
          }
          for ($i = 1; $i < $row; $i++) {
              $col = count($result[$i]);
              for($j=0;$j<$col;$j++)
              {
                  $this->result[$i-1][$this->getDbNameByName($this->titleOrder[$j])] = $result[$i][$j];
              }
          }  
          fclose($handle); //关闭指针 
          return ture;
     }  
     
     public function inputCsv($handle)
     {
               $out = array ();     
               $n = 0;     
               while ($data = fgetcsv($handle, 10000))
               {
                     $num = count($data);         
                     for ($i = 0; $i < $num; $i++) 
                     {
                           $out[$n][$i] = $data[$i];         
                     }         
                     $n++;     
               }     
               return $out; 
     } 

     public function getRowNumber($row,$name,$export=false)
     {
         return $row + 1;
     }
     
     public function writeFile($fileName,$data=null,$withTitle=true)
     {
         

          $handle = fopen($fileName, 'w+');

          if($data==null)
           {
              $data = $this->createCsv($withTitle);
              
           }
         fwrite($handle,$data);
         fclose($handle);
     }
     
     public function writeCsv($fileName,$data=null,$withTitle=true)
     {
         $this->writeFile($fileName,$data,$withTitle);
     }
     

    
          
     public function exportCsv($fileName,$data=null,$withTitle=true,$src=null,$method=null) 
     {
           header("Content-type:text/csv;charset=utf-8"); 
           header('Content-Disposition: attachment; filename="'.$fileName.'"');
           header('Cache-Control:must-revalidate,post-check=0,pre-check=0'); 
           header('Expires:0'); 
           header('Pragma:public'); 
            ob_end_clean();
           if($data==null)
           {
              $data = $this->createCsv($withTitle);
           }
           echo trim($data);
           die();

     }


     public function setResult($result)
     {
          $this->result = $result;
     }
     
     public function setDataMsg($dataMsg)
     {
         $this->result = $dataMsg->getDataArray();
     }
     
     public function getResult()
     {
           return $this->result;
     }
     public function getResultSize()
     {
         if($this->result==null)
         {
             return 0;
         }    
         return count($this->result);
     }
     public function getTitleSize()
     {
         if($this->titleOrder==null)
         {
             return 0;
         }    
         return count($this->titleOrder);
     }
     public function getTitleNameByOrder($order,$row,$isDynamic=true)
     {
         $titleName = $this->getNameByOrder($order);
         $title = $this->getTitleByName($titleName);
         if($isDynamic&&$title['isdynamic'])
         {
             $methodname = $title['methodname'];
             $isexport = $title['isexport'];
             $titleName =  $this->methodName($row,$dbName,$isexport);
         }
         return $titleName;
     }
     public function getTitleNameByDbName($dbname,$row,$isDynamic=true)
     {
        $this->getTitleNameByOrder($this->getOrderByDbName($dbName),$row,$isDynamic);    
     }
     
     
     public function getOrder()
     {
         return $this->titleOrder;
     }
     public function addDynamicTitle($name,$dbName,$methodname,$isExport=true,$orderType="CaseInsensitiveString",$isChecked=true,$width="",$style="")
     {
            $this->addReportTitle($name,$dbName,true,$methodname,$isExport,$orderType,$isChecked,$width,$style);
     }
     public function addTitle($name,$dbName,$orderType="CaseInsensitiveString",$isChecked=true,$width="",$style="")
     {
       $this->addReportTitle($name,$dbName,false,"",true,$orderType,$isChecked,$width,$style);
     }
     public function addShowTitle($name,$dbName,$orderType="CaseInsensitiveString",$isChecked=true,$width="",$style="")
     {
       $this->addReportTitle($name,$dbName,false,"",false,$orderType,$isChecked,$width,$style); 
     }
     public function changeCheckState($dbname,$isChecked=true)
     {
         if($this->title!=null&&$this->title[$name]!=null)
         {
             $this->title[$name]["ischecked"] = $isChecked;
         }
     }
     private function addReportTitle($name,$dbName,$isDynamic,$methodname,$isExport=true,$orderType="CaseInsensitiveString",$isChecked=true,$width="",$style="")
     {
         if(empty($this->title))
         {
             $this->title = array();
         }
         if(empty($this->titleInfo))
         {
              $this->titleInfo = array();
         }
         if(empty($this->titleOrder))
         {
              $this->titleOrder = array();
         }

         $temp = array(
                    'dbname' => $dbName,
                    'name' => $name,
                    'ischecked' => $isChecked,  
                    'style' => $style,
                    'width' => $width,   
                    'ordertype' =>  $orderType,
                    'isdynamic' => $isDynamic,
                    'methodname' => $methodname,
                    'isexport' =>$isExport,
 
         );     
         $this->title[$name] = $temp;
         $this->titleInfo[$dbName] = $temp;
         if($this->getOrderByName($name)==null)
         {
            $this->titleOrder[] = $name;

         }            
     }
     

     
     public function checkTitle()
     {
              
         if(empty($this->title))
         {
             $this->title = array();
         }
         if(empty($this->titleInfo))
         {
             $this->titleInfo = array();
         }
     }
     
      public function checkTitleOrder()
     {
              
         if($this->titleOrder==null)
         {
             $this->titleOrder = array();
         }
     }
     
     
     public function checkStructure()
     {
         if($this->structure==null)
         {
            $this->structure = array();
         }
     }
     public function getNameByDbName($dbName)
     {
         $this->checkTitle();
         foreach ($this->title as $kk=>$vv)
         {
             if($vv['dbname']==$dbName) 
             {
                 return $kk;
             }
         }
     }
   
     public function getOrderByName($name)
     {
          $this->checkTitleOrder();
          for($i=0;$i<count($this->titleOrder);$i++)
          {
               if($this->titleOrder[$i] == $name)
              {
                  return $i;
              }
          }
          return null;
     } 
     public function getNameByOrder($i)
     {
         $this->checkTitleOrder();
         if(i>count($this->titleOrder))
         {
             return null;
         }
         return $this->titleOrder[$i];
     }
     
     public function getDbNameByOrder($i)
     {
         $temp =$this->getNameByOrder($i);
         if($temp==null)
         {
             return null;
         }
         return $this->getDbNameByName($temp);
     }
     
     public function getOrderByDbName($dbName)
     {
        
        return $this->getOrderByName($this->getNameByDbName($dbName));
     } 
     
     public function getTitleByName($name)
     {
         $this->checkTitle();
         if($name == null)
         {
             return null;
         }
         if($this->title[$name]==null)
         {
              die("getTitleByName:please define the of ".$name." at first !");
         }
         return $this->title[$name];
     }

     public function getTitleInfo()
     {
        return $this->titleInfo;
     }

     public function getTitleByDbName($dbName)
     {
         $this->checkTitle();
         if($name == null)
         {
             return null;
         }
         if($this->titleInfo[$dbName]==null)
         {
              die("getTitleByDbName:please define the of ".$dbName."(dbname) at first !");
         }
         return $this->titleInfo[$dbName];
     }
     public function getTitleByOrder($i)
     {
         return $this->getTitleByName($this->getNameByOrder($i));
     }
     public function getStructureByDbName($dbName,$check=true)
     {
  
         $this->checkStructure();
         if($dbName == null)
         {
            return null;
         }
         if($this->structure[$dbName]==null&&$check)
         {
              die("getStructureByDbName:please define the structure of ".$dbName." at first !");
         }

         return $this->structure[$dbName];
     }
     public function getStructureByName($name,$check=true)
     {
         if($name == null)
         {
             return null;
         }
         
         return $this->getStructureByDbName($this->getDbnameByName(),$check);
     }
     public function getStructureByOrder($i,$check=true)
     {
         return $this->getStructureByDbName($this->getDbNameByOrder($i),$check);
     }
     public function getDbNameByName($name)
     {
         if($name == null)
         {
             return null;
         }
         $temp = $this->getTitleByName($name);
         return $temp["dbname"];
         
     } 
     
     public function addCsvStructureByDbName($dbName,$csvName,$csvCol,$isMethod=false,$methodName="",$width="",$style="")
     {
         $this->addStructureByDbName($dbName,$isMethod,$methodName,$width,$style,"csv",$csvName,$csvCol);
     }
     
     public function addCsvStructureByName($dbName,$csvName,$csvCol,$isMethod=false,$methodName="",$width="",$style="")
     {
         $this->addStructureByName($dbName,$isMethod,$methodName,$width,$style,"csv",$csvName,$csvCol);
     }
     
     public function addJsonStructureByDbName($dbName,$jsonName,$jsonCol,$isMethod=false,$methodName="",$width="",$style="")
     {
         $this->addStructureByDbName($dbName,$isMethod,$methodName,$width,$style,"csv",$jsonName,$jsonCol);
     }
     
     public function addJsonStructureByName($dbName,$jsonName,$jsonCol,$isMethod=false,$methodName="",$width="",$style="")
     {
         $this->addStructureByName($dbName,$isMethod,$methodName,$width,$style,"json",$jsonName,$jsonCol);
     }
     
     public function setMethodByDbName($dbName,$isMethod=false,$methodName="") 
     {
         if($dbName == null)
         {
             $dbName = null;
         }
         if($isMethod and $methodName=="")
         {
              $methodName = $dbName;
         }    
         $this->checkStructure();
         $temp =  $this->structure[$dbName];
         $temp['ismethod'] = $isMethod;
         $temp['methodname'] = $methodName;
         $this->structure[$dbName] = $temp;
     }
     public function addStructureByDbName($dbName,$isMethod=false,$methodName="",$width="",$style="",$extendType = "none" , $extendName="",$extendCol = "")
     {
         if($dbName == null)
         {
             $dbName = null;
         }
         if($isMethod and $methodName=="")
         {
              $methodName = $dbName;
         }    
         $this->checkStructure();
         $temp = array(
                    'ismethod' =>  $isMethod,
                    
                    'style' => $style,
                    
                    'width' => $width,
                    
                    'methodname' =>$methodName,
                    
                    'extendType' => $extendType,
                    
                    'extendName' =>$extendName,
                                        
                    'extendCol' => $extendCol,
         );
         $this->structure[$dbName] = $temp;
     }
     
     public function addStructureByName($name,$isMethod=false,$methodName="",$width="",$style="",$extendType="none",$extendCol = "")
     {
         if($name == null)
         {
             return null;
         }
         $dbname = $this->getDbNameByName($name);
         $this->addStructureByDbName($dbname,$isMethod=false,$methodName,$width,$style,$extendType,$extendCol);
     }
     public function clearTitle()
     {
         $this->title = null;
     }
     public function clearStructure()
     {
         $this->structure = null;
     }
     public function clearTitleOrder()
     {
         $this->titleOrder = null;
     }
     
     public function clear($clearData=true)
     {
         $this->clearTitle();
         $this->clearStructure();
         $this->clearTitleOrder();
         if($clearData)
         {
            $this->clearData();
         }
     }
     
      public function clearData()
      {
        $this->result  = Array();
      }
     
     public function getPageData($db,$sql)
     {
        $this->getData($db,$sql,$this->getPageRows(),$this->getCurPage());
     }

     public function getDataByWhere($db,$sql,$where,$dataArray=null,$pagerows=0,$curpage=1)
     {
        $sqlBuilder = new sqlBuilder();
        $sqlBuilder->setSql($sql);
        $sqlBuilder->setWhere($where);
        $sqlBuilder->setData($dataArray);
        $this->getDataBySqlBuilder($db,$sqlBuilder,$pagerows,$curpage);
     }


     
     public function getDataBySqlBuilder($db,$sqlBuilder,$pagerows=0,$curpage=1)
     {
          $this->setSqlBuilder($sqlBuilder);
          $sql = $sqlBuilder->getSql();
          $this->getData($db,$sql,$pagerows,$curpage,$sqlBuilder);
     }
     
     public function getData($db,$sql,$pagerows=0,$curpage=1,$sqlBuilder=null)
     {

           $this->setDb($db);
           $this->setExecSql($sql);
           $this->getSqlBuilder($sqlBuilder);
           $dataMsg = new DataMsg($db);
           $dataMsg->findByPageSql($db,$sql,$pagerows,$curpage,$this->getCountCol(),"",$sqlBuilder);
           $this->totalcount = $dataMsg->getTotalCount();

           $this->pagerows = $dataMsg->getPageRows();
           $this->curpage = $dataMsg->getCurPage();
           $this->totalpages = $dataMsg->getTotalPages();
           $this->result = Array();
           for($i=0;$i<$dataMsg->getSize();$i++)
           {
               $data = $dataMsg->getData($i);
               $this->result[] = $data->getDataArray(); 
           }
       }
     
   
     public function modifyTitleByName($name,$item,$itemValue)
     {
         if($name == null)
         {
             return null;
         }
         $temp = $this->getTitleByName($name);
         $temp[$item] = $itemValue;
         $this->title[$name] = $temp;
     } 
     public function modifyStructureByDbName($dbName,$item,$itemValue)
     {
         if($dbName == null)
         {
             $dbName = null;
         }
         $temp = $this->getStructureByDbName($dbName);
         $temp[$item] = $itemValue;
         $this->structure[$name] = $temp;
     }
     public function  modifyStructureByName($name,$item,$itemValue)
     {
         
         if($name == null)
         {
             return null;
         }
         $this->modifyStructureByDbName($this->getDbNameByName($name),$item,$itemValue);
     }
     
     
     public function getOriValueByName($row,$dbName,$isTableItem=true,$isExoprt=false,$customMethod=false,$customMethodName=null)
     {
         return $this->getValueByName($row, $dbName,$isTableItem,$isExoprt,$customMethod,$customMethodName,true);
     }
     
     public function getOriValueByOrder($row,$dbName,$isTableItem=true,$isExoprt=false,$customMethod=false,$customMethodName=null)
     {
         return $this->getValueByOrder($row, $dbName,$isTableItem,$isExoprt,$customMethod,$customMethodName,true);
     }
     
     public function getOriValueByCsvCol($row,$csvName,$csvCol)
     {
         $result =  $this->result[$row][$csvName];
         return $this->getCsvValueByString($row, $csvName, $csvCol, $result);
     }
     
     public function getOriValueByJsonCol($row,$jsonName,$jsonCol)
     {
         $result =  $this->result[$row][$csvName];
          return $this->getJsonValueByString($row, $jsonName, $jsonCol, $result);
     }
     
     public function getOriValueByExtendCol($row,$extendName,$extendCol)
     {
          $result =  null;
          $temp = $this->getStructureByDbName($extendName,false); 
          if(is_array($temp))
          {
              if($temp['extendType']=="csv")
              {                           
                $result =  $this->getOriValueByCsvCol($row,$extendName,$extendCol);
              }   
              else if($temp['extendType']=="json")
              {
                 $result =  $this->getOriValueByJsonCol($row,$extendName,$extendCol);
              }
          }
          return $result;
     }
     
     public function getOriValueByDbName($row,$dbName,$forceOri=false)
     {
           
           $result =  $this->result[$row][$dbName];
           
           $temp = $this->getStructureByDbName($dbName,false); 
           if(is_array($temp)&&!$forceOri) 
           {
              $csvCol = $temp['extendCol'];
              $csvName = $temp['extendName'];
              if($temp['extendType']=="csv")
              {                           
                $result =  $this->getOriValueByCsvCol($row,$csvName,$csvCol);
              }   
              else if($temp['extendType']=="json")
              {
                 $result =  $this->getOriValueByJsonCol($row,$csvName,$csvCol);
              }
           
           }
           
           return $result;
     }
     
     
     public function getCsvValue($row,$dbName,$csvName,$csvCol)
     {
          $result =  $this->result[$row][$dbName];
          return $this->getCsvValueByString($row, $csvName, $csvCol, $result);
     }   
     public function geJsonValue($row,$dbName,$csvName,$csvCol)
     {
          $result =  $this->result[$row][$dbName];
          return $this->getJsonValueByString($row, $csvName, $csvCol, $result);
     }   
     
 
     public function getValueByDbName($row,$dbName,$isTableItem=true,$isExoprt=false,$customMethod=false,$customMethodName=null,$forceOri=false)
     {
              
          $result =  $this->getOriValueByDbName($row,$dbName,$forceOri);
          if($isTableItem)
          {

             if($customMethod)
             {
                   if($customMethodName==null)
                   {
                       $customMethodName = $dbName;
                   }     
                   $result = $this->$customMethodName($row,$dbName,$isExoprt);
             }
             else {
               
                 $temp = $this->getStructureByDbName($dbName);
                 
                 if($temp['ismethod'])
                 {
                   $methodName = $temp['methodname'];
                   $result =   $this->$methodName($row,$dbName,$isExoprt);
                 }
             }
          }
           if($result==null||trim($result)=="")
           {   
                $defaultValue = $this->defaultValue[$dbName];

                if(trim(strval($defaultValue))!="")
                {
                    $result = $defaultValue;
                }
           }
          return $result;
     }
     
     public function getValueByName($row,$name,$isTableItem=true,$isExoprt=false,$customMethod=false,$customMethodName="",$forceOri=false)
     {
         return $this->getValueByDbName($row, $this->getDbNameByName($name),$isTableItem,$isExoprt,$customMethod,$customMethodName,$forceOri);
     }
     public function getValueByOrder($row,$i,$isTableItem=true,$isExoprt=false,$customMethod=false,$customMethodName="",$forceOri=false)
     {
         return $this->getValueByDbName($row, $this->getDbNameByOrder($i),$isTableItem,$isExoprt,$customMethod,$customMethodName,$forceOri);
     }
 }

class reportSelector{
      private $reportsOrder;
      private $reports;
      public function clear()
      {
          $this->reportsOrder=null;
          $this->reports=null;
      }
      public function addReport($reportName,$reporeObj,$changeReportName=true)
      {
              
          if($changeReportName)
          {
              $reporeObj->setReportName($reportName);
          }
          if(empty($this->reportsOrder))
          {
              $this->reportsOrder = array();
          }
          if(empty($this->reports))
          {
              $this->reports = array();
          }
          if(empty($this->reportsOrder))
          {
             $this->reportsOrder = array();
          }
          $this->reports[$reportName] = $reporeObj;
          
          if($this->getOrderByName($reportName)==null)
          {
              $this->reportsOrder = array_merge($this->reportsOrder,array($reportName));
              
          }
      }
      public function checkReportsOrder()
      {
         if($this->reportsOrder==null)
         {
             $this->reportsOrder = array();
         }
      }
      public function checkReports()
      {
         if($this->reports==null)
         {
                 $this->reports = array();
         }
      }
      
      
      public function getOrderByName($reportName)
      {
   
          $this->checkReportsOrder();
          for($i=0;$i<count($this->reportsOrder);$i++)
          {
              if($this->reportsOrder[$i]==$reportName)
              { 
                  return $i;
              }
          }
          return null;
      }
      public function getNameByOrder($i)
      {
          $this->checkReportsOrder();
          return $this->reportsOrder[$i];
      }
      public function getReportByName($reportName)
      {
          if($reportName==null)
          {
              return null;
          }
           $this->checkReports();
           return $this->reports[$reportName];
      }
      public function getReportByOrder($i)
      {
          return $this->getReportByName($this-> getNameByOrder($i));
      }
      
      public function getReportsSize()
      {
          if($this->reportsOrder==null)
         {
             return 0;
         }    
         return count($this->reportsOrder);
          
      }
    
}
 
 class categoryReport extends report
 {
     protected $categoryResult=null;
     protected $order =null;
     protected $bakAll = null;
     protected $selected = null;
    
     public function getSelected()
     {
        return $this->selected;

     }
   
     public function createCategoryCsv($isMutilTitle=true,$allowBlank=false)
     {
        $bak = $this->result;
        $str = "";    
          if(!$isMutilTitle)
          {
              $str.=$this->createTitleCsv();
          }
        for($j=0;$j<$this->getCategorySize();$j++)
        {
         
                $name = $this->getCategoryName($j);
            
                $this->selectCategory($name);
                if($allowBlank||$this->getResultSize()>0)
                {
                    $withTitle = false;
                    if($isMutilTitle)
                    {
                        $withTitle = true;
                    }
                     $str.=$name."\n";
                     $str.=$this->createCsv($withTitle);
                }
                
        }
        $this->result = $bak;
        return $str;
     }

     public function createCategoryObj($isMutilTitle=true,$allowBlank=false)
     {
        $bak = $this->result;
        $row = 1;    
        $exportObj = new PHPExcel();
        $exportObj->createSheet();
        $exportObj->setActiveSheetIndex(0);
        $exportObj->getDefaultStyle()
                ->getBorders()
                ->getTop()
                    ->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
        $exportObj->getDefaultStyle()
                ->getBorders()
                ->getBottom()
                    ->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
        $exportObj->getDefaultStyle()
                ->getBorders()
                ->getLeft()
                    ->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
        $exportObj->getDefaultStyle()
                ->getBorders()
                ->getRight()
                    ->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
          if(!$isMutilTitle)
          {
              for($i=0;$i<count($this->titleOrder);$i++)
              {
                 $title = $this->getTitleByOrder($i);
                  if($title["isexport"])
                  {
                    $value = $this->titleOrder[$i];
                    $exportObj->getActiveSheet()->setCellValueExplicit(PHPExcel_Cell::stringFromColumnIndex($i).$row, $value,PHPExcel_Cell_DataType::TYPE_STRING);
                    
                  }
                 
              }
             $row++;
          }
        for($j=0;$j<$this->getCategorySize();$j++)
        {
         
                $name = $this->getCategoryName($j);
            
                $this->selectCategory($name);
                if($allowBlank||$this->getResultSize()>0)
                {
                    $withTitle = false;
                    if($isMutilTitle)
                    {
                        $withTitle = true;
                    }
                    
                }
                        if($withTitle)
                        {
                               for($i=0;$i<count($this->titleOrder);$i++)
                              {
                                 $title = $this->getTitleByOrder($i);
                                  if($title["isexport"])
                                  {
                                    $value = $this->titleOrder[$i];
                                    $exportObj->getActiveSheet()->setCellValueExplicit(PHPExcel_Cell::stringFromColumnIndex($i).$row, $value,PHPExcel_Cell_DataType::TYPE_STRING);
                                    
                                  }
                                 
                              }
                             $row++;
                        } 
                for($i=0;$i<$this->getResultSize();$i++)
                {
                        for($j=0;$j<count($this->titleOrder);$j++)
                        {
                             $title = $this->getTitleByOrder($j);
                             if($title["isexport"])
                             {
                                $value = $this->getValueByOrder($i,$j,true,true);
                                $exportObj->getActiveSheet()->setCellValueExplicit(PHPExcel_Cell::stringFromColumnIndex($j).$row, $value,PHPExcel_Cell_DataType::TYPE_STRING);
                              
                             }
                           
                       }        
                       $row++;
                } 
               
        }
        $exportObj->getActiveSheet()->getStyle('A1:'.PHPExcel_Cell::stringFromColumnIndex($j).$row)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
        $this->result = $bak;
         return $exportObj;
     }
     
     public function getAll()
     {
        $this->result = $this->bakAll;
     }
     public function getCategoryResult()
     {
         return $this->categoryResult;
     }
     
     public function setOrder($order)
     {
         $this->order = $order;
     }
     
     public function getCategoryName($i)
     {
         return $this->order[$i];
     }
     
     public function getCategorySize($categoryResult=false)
     {
         $result = count($this->order);
         if($categoryResult)
         {
              $result = count($this->categoryResult);
         }
         return  $result;
     }
     
     public function addDataArrayByKey($key,$data)
     {
         $this->categoryResult[$key][] = $data;
     }
     
     public function addDataArray($key,$data)
     {
         if($this->categoryResult==null)
         {
             $this->categoryResult = Array();
         }
         $value = $data[$key];
         $this->categoryResult[$value][] = $data;
     }
     public function getCategoryData($db,$sql,$key,$pagerows=0,$curpage=1)
     {
           parent::getData($db,$sql,$pagerows,$curpage);
           $this->bakAll = $this->result;
           $this->addArray($key,$this->result);
          

     }
     public function addData($key,$data)
     {
          $this->addDataArray($data->getDataArray(),$key);
     }
     
     public function addArray($key,$array)
     {
         for($i=0;$i<count($array);$i++)
         {
             $arr = $array[$i];
             $this->addDataArray($key,$arr);
         }   
        
     }
     public function addDataMsg($dataMsg,$key)
     {
         for($i=0;$i<$dataMsg->getSize();$i++)
         {
             $data = $dataMsg->getData($i);
             $this->addData($key,$data);
         }      
     }
     public function order()
     {
         $this->order = Array();
         $temp = $this->categoryResult;
         foreach($temp as $key=>$value)
         {  
            $this->order[] = $key;
         }
     }
     public function selectCategory($categoryName)
     {
         $temp = Array();
         $this->selected = $categoryName;
         if(isset($this->categoryResult[$categoryName]))
         {
             $temp = $this->categoryResult[$categoryName];
         }
         $this->result = $temp;
     }
     public function setCat($i)
     {
        $categoryName = $this->order[$i];
        $this->selectCategory($categoryName);
     }   
 }