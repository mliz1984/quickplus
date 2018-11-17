<?php
     namespace Quickplus\Lib;
     set_time_limit(0);
     use Quickplus\Lib\DataMsg\DataMsg;
 
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