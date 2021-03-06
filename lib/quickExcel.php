<?php
namespace Quickplus\Lib;
require_once($_SERVER['DOCUMENT_ROOT']."/lib/PHPExcel.php");
require_once($_SERVER['DOCUMENT_ROOT']."/lib/parameters.php");
use Quickplus\Lib\Tools\FileTools as FileTools;
use Quickplus\Lib\Tools\StringTools as StringTools;
use Quickplus\Lib\QuickFormConfig;

	class QuickExcel 
	{
		protected $cellData = Array();
		protected $colData = Array();
		protected $templateFile = null;
		protected $objPHPExcel = null;
		protected $startRow = 1;
		protected $colWidth = null;
		protected $sheetName = null;
	    protected $attachSheet = Array();
	    public  function setCellDataFromArray($array,$mappingArray,$startRow=1,$attachSheetId=null,$exportImageSetting=Array())
	    {
	        $cellData = Array(); 
	    	for($i=0;$i<count($array);$i++)
	    	{
	    		$row = $startRow + $i;
	    		foreach($mappingArray as $col =>$key)
	    		{
	    			$data = $array[$i][$key];
	    			$isImg = false;
	    			if(isset($exportImageSetting[$key])&&is_array($exportImageSetting[$key]))
	    			{
	    				$isImg =true;
	    			}
	    			if($attachSheetId!=null&&trim($attachSheetId)!="")
	    			{
	    				if($isImg)
	    				{
	    					$this->setAttachSheetCellImg($attachSheetId,$col,$row,$exportImageSetting[$key]["height"],$exportImageSetting[$key]["width"],$data);
	    				}
	    				else
	    				{
	    					$this->setAttachSheetCellData($attachSheetId,$col,$row,$data);
	    				}
	    			}
	    			else
	    			{
	    				if($isImg)
	    				{
	    					$this->setCellImg($col,$row,$exportImageSetting[$key]["height"],$exportImageSetting[$key]["width"],$data);
	    				}
	    				else
	    				{
							$this->setCellData($col,$row,$data);	
					    }	
	    			}
	    		}
	    		if($attachSheetId!=null&&trim($attachSheetId)!="")
	    		{
	    			$this->startRow = $startRow;
	    		}
	    		else
	    		{	
	    			$this->setAttachSheetStartRow($id,$startRow);
	    		}
	    	}
	    }
	    public function setAttachSheet($sheetid,$sheetname)
	    {
	    	if(!is_array($this->attachSheet[$sheetid]))
	    	{
				$this->attachSheet[$sheetid] = Array();
	    	}
	    	$this->attachSheet[$sheetid]["sheetid"] = $sheetid;
	        $this->attachSheet[$sheetid]["sheetname"] = $sheetname;
	        $this->attachSheet[$sheetid]["startrow"] = 1;
			$this->attachSheet[$sheetid]["celldata"] = Array();
			$this->attachSheet[$sheetid]["coldata"] = Array();
	    }
	     public function setAttachSheetStartRow($sheetid,$startRow)
	    {
	    	if(!is_array($this->attachSheet[$sheetid]))
	    	{
				$this->attachSheet[$sheetid] = Array();
	    	}
	    	$this->attachSheet[$sheetid]["startrow"] = $startRow;
	    }
	    
	    public function setAttachSheetCellData($sheetid,$col,$row,$data,$method=null)
	    {
	    	if(!is_array($this->attachSheet[$sheetid]))
	    	{
				$this->attachSheet[$sheetid] = Array();
	    	}
	    	$this->attachSheet[$sheetid]["celldata"][$col.$row] =  Array("col"=>$col,"row"=>$row,"data"=>$data,"method"=>$method,"type"=>"normal");
	    }
	    public function setAttachSheetCellImg($sheetid,$col,$row,$height,$width,$picpath,$method=null)
	    {
	    	if(!is_array($this->attachSheet[$sheetid]))
	    	{
				$this->attachSheet[$sheetid] = Array();
	    	}
	    	if(filter_var($picpath, FILTER_VALIDATE_URL, FILTER_FLAG_SCHEME_REQUIRED))
			{
	    		$this->attachSheet[$sheetid]["celldata"][$col.$row] = Array("col"=>$col,"row"=>$row,"path"=>$picpath,"method"=>$method,"type"=>"img","height"=>$height,"width"=>$width,"method"=>$method,);
	    	}
	    	else
	    	{
	    		$this->attachSheet[$sheetid]["celldata"][$col.$row] =  Array("col"=>$col,"row"=>$row,"data"=>$picpath,"method"=>$method,"type"=>"normal");
	    	}
	    }

	    public function setAttachSheetCellImage($sheetid,$col,$row,$height,$width,$picpath,$isAbsolutePath=true,$method=null)
	    {

	    	if(!is_array($this->attachSheet[$sheetid]))
	    	{
				$this->attachSheet[$sheetid] = Array();
	    	}
	        if(!$isAbsolutePath)
			{
				$picpath = FileTools::getRealPath($picpath);
			}
			if(file_exists($picpath))
			{
	    		$this->attachSheet[$sheetid]["celldata"][$col.$row] = Array("col"=>$col,"row"=>$row,"data"=>$picpath,"method"=>$method,"type"=>"image","height"=>$height,"width"=>$width);
	    	}
	    	else
	    	{
	    		$this->attachSheet[$sheetid]["celldata"][$col.$row] =  Array("col"=>$col,"row"=>$row,"data"=>$picpath,"method"=>$method,"type"=>"normal");
	    	}
	    }

		public function setSheetName($sheetName)
		{
			$this->sheetName = $sheetName;
		}
		public function getSheetName()
		{
			return $this->sheetName;
		}
		public function setStartRow($startRow)
		{
			$this->startRow = $startRow;
		}
		public function setColData($col,$data,$method=null)
		{
			$this->colData[$col] = Array("col"=>$col,"data"=>$data,"method"=>$method,"type"=>"normal");
		} 
		public function setColImage($col,$height,$width,$picpath,$isAbsolutePath=true,$method=null)
		{
				if(!$isAbsolutePath)
				{
					$picpath = FileTools::getRealPath($picpath);
				}
				if(file_exists($picpath))
				{
					$this->colData[$col] = Array("col"=>$col,"data"=>$picpath,"method"=>$method,"type"=>"image","height"=>$height,"width"=>$width);
				}
				else
				{
						$this->colData[$col] = Array("col"=>$col,"data"=>$picpath,"method"=>$method,"type"=>"normal");
				}
		}
		public function setColImg($col,$height,$width,$picpath,$method=null)
		{
			   if(filter_var($picpath, FILTER_VALIDATE_URL, FILTER_FLAG_SCHEME_REQUIRED))
			   {
				 $this->colData[$col] = Array("col"=>$col,"data"=>$picpath,"method"=>$method,"type"=>"img","height"=>$height,"width"=>$width);
			   }
			   else
			   {
			   	$this->colData[$col] = Array("col"=>$col,"data"=>$picpath,"method"=>$method,"type"=>"normal");
			   }
		}
		public function setCellImage($col,$row,$height,$width,$picpath,$isAbsolutePath=true,$method=null)
		{
				if(!$isAbsolutePath)
				{
					$picpath = FileTools::getRealPath($picpath);
				}
				if(file_exists($picpath))
				{
					$this->cellData[$col.$row] = Array("col"=>$col,"row"=>$row,"data"=>$picpath,"method"=>$method,"type"=>"image","height"=>$height,"width"=>$width);
				}
				else
				{
					$this->cellData[$col.$row] = Array("col"=>$col,"row"=>$row,"data"=>$picpath,"method"=>$method,"type"=>"normal");
				}
		}
		public function setCellImg($col,$row,$height,$width,$picpath,$method=null)
		{
			 if(filter_var($picpath, FILTER_VALIDATE_URL, FILTER_FLAG_SCHEME_REQUIRED))
			   {
				 $this->cellData[$col.$row] = Array("col"=>$col,"row"=>$row,"data"=>$picpath,"method"=>$method,"type"=>"img","height"=>$height,"width"=>$width);
			   }
			   else
			   {
			   	$this->cellData[$col.$row] = Array("col"=>$col,"row"=>$row,"data"=>$picpath,"method"=>$method,"type"=>"normal");
			   }
		}
		public function setCellData($col,$row,$data,$method=null)
		{
			$this->cellData[$col.$row] = Array("col"=>$col,"row"=>$row,"data"=>$data,"method"=>$method,"type"=>"normal");
		}
		public function getCellData()
		{
			return $this->cellData;
		}

		public function setTemplateFile($file,$isAbsolutePath=true)
		{
			if(!$isAbsolutePath)
			{
				$file = FileTools::getRealPath($file);
			}
			$this->templateFile = $file;
		}

		public function getCellValue($col,$row,$sheetIndex)
		{
			$result = null;
			if($this->objPHPExcel!=null)
			{
				$objPHPExcel->setActiveSheetIndex($sheetIndex);
				$result = $objPHPExcel->getActiveSheet()->getCell($col.$row)->getValue();
			}
			return $result;

		}

		public function init($src=null)
		{

		}

		protected function processCellData($objPHPExcel,$row,$dataArray,$newMark)
		{
			    $col = $dataArray["col"];
				$newValue = $dataArray["data"];
				$method= $dataArray["method"];
				$type = $dataArray["type"];
				$oldValue = null;
				$cellwidth = ceil($objPHPExcel->getActiveSheet()->getColumnDimension($col)->getWidth()*3.7795*10); 
				$cellheight = ceil($objPHPExcel->getActiveSheet()->getRowDimension($row)->getRowHeight()*10);
				$curColWidth = null;
				$add = true;
				if($type=="img"&&!empty($newValue))
				{
					  if(filter_var($newValue, FILTER_VALIDATE_URL, FILTER_FLAG_SCHEME_REQUIRED))
			   		 {
						$add = false;
						$height = $dataArray["height"];
						$width = $dataArray["width"];
						$picpath = $newValue;
						$ext = FileTools::getExtension($picpath);
						$image = null;
						if($ext=="jpg" || $ext=="jpeg")
						{
							$image = imagecreatefromjpeg($picpath);
						}
						else if($ext=="png")
						{
							$image = imagecreatefrompng($picpath);
						}
						else if($ext=="gif")
						{
							$image = imagecreatefromgif($picpath);
						}
						else if($ext=="bmp")
						{
							$image = imagecreatefrombmp($picpath);
						}

						$objPHPExcel->getActiveSheet()->getColumnDimension()->setAutoSize(true);
						$objPHPExcel->getActiveSheet()->getRowDimension($row)->setRowHeight($height);
						$objDrawing = new \PHPExcel_Worksheet_MemoryDrawing();
						$objDrawing->setCoordinates($col.$row); 
						$objPHPExcel->getActiveSheet()->getStyle($col.$row)->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
   					    $objPHPExcel->getActiveSheet()->getStyle($col.$row)->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);
					    $objDrawing->setImageResource($image);	
						$objDrawing->setRenderingFunction(\PHPExcel_Worksheet_MemoryDrawing::RENDERING_DEFAULT);//渲染方法
						$objDrawing->setMimeType(\PHPExcel_Worksheet_MemoryDrawing::MIMETYPE_DEFAULT);
						if($height!=null&&trim($height)!="")
							{	
								$objDrawing->setHeight($height);
							}  
							if($width!=null&&trim($width)!="")
							{
								$objDrawing->setWidth($width);
							}  
					     $objDrawing->setWorksheet($objPHPExcel->getActiveSheet()); 
					}
				}
				else if($type=="image")
				{
						$add = false;
						$height = $dataArray["height"];
						$width = $dataArray["width"];
						 $curColWidth = intval($width);
						 if($method!=null&&trim($method)!="")
					    {
							$picpath = $this->$method($col,$row,$oldValue,$newValue);
					    }
						$picpath = $newValue;
						$oldValue = null;
						$objDrawing = new \PHPExcel_Worksheet_Drawing();  
						$objDrawing->setPath($picpath);
					    $objDrawing->setCoordinates($col.$row);
						if($height!=null&&trim($height)!="")
						{
					
							$offsety = ($cellheight-$height)/2;
							$objDrawing->setHeight($height);
							
							$objDrawing->setOffsetY($offsety);
							$objPHPExcel->getActiveSheet()->getRowDimension($row)->setRowHeight($height);	
							
						}  
						if($width!=null&&trim($width)!="")
						{

							$offsetx = ($cellwidth-$width)/2;
							$objDrawing->setOffsetX($offsetx);
							$objDrawing->setWidth($width);
						}  

						
	
					    $objDrawing->setWorksheet($objPHPExcel->getActiveSheet()); 
					    

				}
				if($add)
				{
					if(!$newMark)
					{
						$oldValue = $objPHPExcel->getActiveSheet()->getCell($col.$row)->getValue();
						$oldValue = StringTools::conv($oldValue);
					}
			
					if($method!=null&&trim($method)!="")
					{
						$newValue = $this->$method($col,$row,$oldValue,$newValue);
					}
					$newValue  =StringTools::conv($newValue);
	                $objPHPExcel->getActiveSheet()->setCellValueExplicit($col.$row, $newValue ,\PHPExcel_Cell_DataType::TYPE_STRING);
	          	    $objPHPExcel->getActiveSheet()->getStyle($col.$row)->getAlignment()->setWrapText(true);
	          	    $curColWidth = strlen($newValue);
					$objPHPExcel->getActiveSheet()->getStyle($col.$row)->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				}
				    $maxColWidth = intval($this->colWidth[$col]);
	               	if($curColWidth>$maxColWidth)
					{
					     $this->colWidth[$col] = $curColWidth;
					     $objPHPExcel->getActiveSheet()->getColumnDimension($col)->setWidth($curColWidth); 
					}
				return $objPHPExcel;
		}

		public function getColMark($int,$start=0)
	    {
	        $int  = $int  - $start;
	        return PHPExcel_Cell::stringFromColumnIndex($int);
	    }

		public function getExcelData($sheetIndex=0,$src=null,$objPHPExcel=null,$processAttach=true)
		{
			$this->init($src);
			$newMark = null;
            $this->colWidth = Array();
			if($objPHPExcel==null)
			{	
				if($this->templateFile!=null||file_exists($this->templateFile))
				{
					$objPHPExcel = \PHPExcel_IOFactory::load($this->templateFile);
					$this->objPHPExcel = $objPHPExcel;
					$newMark = false;
				}
				else 
				{
					$objPHPExcel = new \PHPExcel();
					$newMark = true;
				}
			}
			for($i = $objPHPExcel->getSheetCount();$i<=$sheetIndex;$i++)
			{
				$objPHPExcel->createSheet();
			}
			$objPHPExcel->setActiveSheetIndex($sheetIndex);
			$objPHPExcel->getDefaultStyle()
			    ->getBorders()
			    ->getTop()
			        ->setBorderStyle(\PHPExcel_Style_Border::BORDER_THIN);
			$objPHPExcel->getDefaultStyle()
			    ->getBorders()
			    ->getBottom()
			        ->setBorderStyle(\PHPExcel_Style_Border::BORDER_THIN);
			$objPHPExcel->getDefaultStyle()
			    ->getBorders()
			    ->getLeft()
			        ->setBorderStyle(\PHPExcel_Style_Border::BORDER_THIN);
			$objPHPExcel->getDefaultStyle()
			    ->getBorders()
			    ->getRight()
			        ->setBorderStyle(\PHPExcel_Style_Border::BORDER_THIN);
			$sheetName = $this->getSheetName();
			if($sheetName!=null&&trim($sheetName)!="")
			{
				 $objPHPExcel->getActiveSheet()->setTitle($sheetName);
			}
			$objPHPExcel->getActiveSheet()->getPageSetup()->setScale(0);
			foreach($this->cellData as $p=>$d)
			{		
				$objPHPExcel = 	$this->processCellData($objPHPExcel,$d["row"],$d,$newMark);

			}	
			if(count($this->colData)>0)
			{
				$allRow= $objPHPExcel->getActiveSheet()->getHighestRow();
				for($i=$this->startRow;$i<=$allRow;$i++)
				{
					foreach($this->colData as $p=>$d)
					{
						$objPHPExcel = 	$this->processCellData($objPHPExcel,$i,$d,$newMark);      
					}
				}
			}
			if($processAttach)
			{
				foreach($this->attachSheet as $sheetid =>$sheetinfo)
				{
					$newSheetIndex = $sheetIndex + 1;
				    $this->colData = $sheetinfo["coldata"];
					$this->cellData = $sheetinfo["celldata"];
					$this->startRow = $sheetinfo["startrow"];
					$this->sheetName = $sheetinfo["sheetname"];
					$objPHPExcel = $this->getExcelData($newSheetIndex,$src,$objPHPExcel,false);
				}
			}
			$objPHPExcel->setActiveSheetIndex(0);
            return $objPHPExcel;
		}

		public function saveFile($fileName,$isAbsolutePath=true,$sheetIndex=0,$src=null)
		{
			if(!$isAbsolutePath)
			{
				$fileName = FileTools::getRealPath($fileName);
			}
			$type = FileTools::getExtension($fileName);
			return $this->createFile($type,$fileName,false,$sheetIndex,$src);
		}

       public function exportFile($fileName,$sheetIndex=0,$src=null)
       {
       		$type = FileTools::getExtension($fileName);
       		$this->createFile($type,$fileName,true,$sheetIndex,$src);
       }

        public function getHtml($sheetIndex=0,$src=null)
        {
        	$objWriter = null;
			$objPHPExcel = $this->getExcelData($sheetIndex,$src);
			$objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'HTML');
            $objWriter->save('php://output');
        }
		

		protected  function createFile($type,$fileName,$isExport=false,$sheetIndex=0,$src=null,$objPHPExcel=null)
		{
			$type = strtolower($type);
			$objWriter = null;
			if($objPHPExcel==null)
			{
				$objPHPExcel = $this->getExcelData($sheetIndex,$src);
			}
			$headerStr = "";
			$saveFileName = $fileName;
			if($type=="xlsx")
			{
				$objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007'); 
				$headerStr = 'Content-Type: application/vnd.ms-excel;charset=utf-8';
			}
			else if($type=="xls")
			{
				$objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5'); 
				$headerStr = 'Content-Type: application/vnd.ms-excel;charset=utf-8';
			}
			else if($type=="pdf")
			{
				if(QuickFormConfig::$quickPdfRenderer!=null&&trim(QuickFormConfig::$quickPdfRenderer)!="")
				{
					$libpath = FileTools::getRealPath(QuickFormConfig::$quickPdfRendererPath);
					if(is_dir($libpath))
					{
						\PHPExcel_Settings::setPdfRenderer(QuickFormConfig::$quickPdfRenderer,$libpath);
					}
				}
				$objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'PDF'); 
				$objWriter->SetFont('arialunicid0-chinese-simplified');
				$headerStr = 'Content-Type: application/pdf;charset=utf-8';
			}
			else if($type=="csv")
			{
				$objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'CSV'); 
				$headerStr = 'Content-Type: text/csv;charset=utf-8';
			}
			if($isExport)
			{
				 header($headerStr);
				 header('Content-Disposition: attachment;filename="'.$fileName.'"');  
				 if($type=="csv")
				 {
				 	 header('Cache-Control:must-revalidate,post-check=0,pre-check=0'); 
          			 header('Expires:0'); 
           			 header('Pragma:public'); 
				 }
				 else
				 {
          		 	header('Cache-Control: max-age=0');
          		 }
          		 ob_end_clean();
          		 $saveFileName = 'php://output';
			}
			$objWriter->save($saveFileName);   
			if($isExport)
			{
				 die();
			}
			else
			{
				return $saveFileName;
			}

		}





	}
?>