<?php
namespace Quickplus\Lib;

use Quickplus\Lib\Tools\FileTools as FileTools;
use Quickplus\Lib\PHPExcel;
use Quickplus\Lib\QuickHtmlWriter;
use Quickplus\Lib\QuickFormConfig;

    class QuickExcelReader
    {
    	protected $excelObj = null; 
    	protected $resPath = null;
    	protected $htmlWriterExtend = null;
    	protected $customFooterHtml = "";
    	protected $customHeaderHtml = "";
    	protected $encode = null;
    	protected $tableId = null;
    	public function setTableId($tableId)
    	{
    		$this->tableId = $tableId;
    	}
    	public function getTableId()
    	{
    		return $this->tableId;
    	}
        public function setEncode($encode)
        {
        	$this->encode = $encode;
        }
        public function getEncode()
        {
        	$result = $this->encode;
        	if($result==null||trim($this->encode)=="")
        	{
        		$result = QuickFormConfig::$encode;
        	}
        	return $result;
        }
    	public function setCustomFooterHtml($customFooterHtml)
		{
			$this->customFooterHtml = $customFooterHtml;
		}
		public function setCustomHeaderHtml($customHeaderHtml)
		{

			$this->customHeaderHtml = $customHeaderHtml;
		}
    	public function setHtmlWriterExtend($htmlWriterExtend)
    	{
    		$this->htmlWriterExtend = $htmlWriterExtend;
    	}
    	public function setResPath($resPath,$autoCreate=true)
    	{
    		
    		$this->resPath = $resPath;
			if($autoCreate)
			{
				$resPath = FileTools::getRealPath($resPath);
				
				FileTools::createDir($resPath);
			}
    	}

    	public function setExcelObj($excelObj)
    	{
    		$this->excelObj = $excelObj;
    	}

    	public function loadFile($filename,$isAbsolutePath=true)
    	{
    		$result = false;
    		if(!$isAbsolutePath)
			{
				$filename = FileTools::getRealPath($filename);
			}
    		if(file_exists($filename)&&is_file($filename))
    		{
    			$this->excelObj = PHPExcel_IOFactory::load($filename);
    			$result = true;
    		}
    		return $result;
    	}


        protected function checkEmpty($array)
		{
			$load = true;
			if($this->skipEmpty)
			{
				$load = false;
				foreach($array as $k => $v)
				{
					if($v!=null&&trim($v)!="")
					{
						$load = true;
						break;
					}
				}
			}
			return $load;
		}
        
    	public function getImageArray()
    	{
    		$result = Array();
    		foreach ($this->excelObj->getActiveSheet()->getDrawingCollection() as $drawing) {

		    if ($drawing instanceof PHPExcel_Worksheet_MemoryDrawing) {
			        ob_start();
			        call_user_func(
			            $drawing->getRenderingFunction(),
			            $drawing->getImageResource()
			        );
			        $imageContents = ob_get_contents();
			        ob_end_clean();
			        switch ($drawing->getMimeType()) {
			            case PHPExcel_Worksheet_MemoryDrawing::MIMETYPE_PNG :
			                    $extension = 'png'; break;
			            case PHPExcel_Worksheet_MemoryDrawing::MIMETYPE_GIF:
			                    $extension = 'gif'; break;
			            case PHPExcel_Worksheet_MemoryDrawing::MIMETYPE_JPEG :
			                    $extension = 'jpg'; break;
			        }
			    } else {

			        $zipReader = fopen($drawing->getPath(),'r');
			        $imageContents = '';
			        while (!feof($zipReader)) {
			            $imageContents .= fread($zipReader,1024);
			     	 }
			        fclose($zipReader);
			        $extension = $drawing->getExtension();
			    }
			        $fileName = FileTools::connectPath($this->resPath,$drawing->getCoordinates().'.'.$extension);
			        $saveFileName = FileTools::getRealPath($fileName);
			        file_put_contents($saveFileName,$imageContents);

			        $result[$drawing->getCoordinates()]=Array("filename"=>$fileName,"height"=>$drawing->getHeight(),"width"=>$drawing->getWidth(),"offsetX"=>$drawing->getOffsetX(),"offsetY"=>$drawing->getOffsetY());
		   	}
			return $result;		    
   	 	}

   	 	protected function getHtmlData()
   	 	{
   	 		$objPHPExcel = $this->excelObj;
			foreach($this->getImageArray() as  $coordinate => $picinfo)
			{
				$objDrawing = new PHPExcel_Worksheet_Drawing();  
				$objDrawing->setCoordinates($coordinate);
				$objDrawing->setPath(FileTools::getRealPath($picinfo["filename"]));
				$objDrawing->setHeight($picinfo["height"]);
				$objDrawing->setOffsetX($picinfo["offsetX"]);
				$objDrawing->setOffsetY($picinfo["offsetY"]);
				$objDrawing->setWorksheet($objPHPExcel->getActiveSheet()); 
				$row =  $objPHPExcel->getActiveSheet()->getCell($coordinate)->getRow();
				$objPHPExcel->getActiveSheet()->getRowDimension($row)->setRowHeight($picinfo["height"]*2-$picinfo["offsetY"]);
			}
			return $objPHPExcel;
   	 	}

   	 	public function saveHtml($filename,$isAbsolutePath=true,$autoCreate=true)
   	 	{
     	 	if(!$isAbsolutePath)
  	    {
  				$filename = FileTools::getRealPath($filename);
  			}
        $encode =  FileTools::detectFileEncode($filename);
        $this->setEncode($encode);
  			$objWriter = $this->getHtmlWriter();
      	$objWriter->save($filename);
   	 	}
   	 	protected function getHtmlWriter($encode=null)
   	 	{
   	 		 $objWriter = new QuickHtmlWriter($this->getHtmlData());
			    $objWriter->setEncode($this->getEncode());
        $objWriter->setCustomFooterHtml($this->customFooterHtml);
   	 		$objWriter->setCustomHeaderHtml($this->customHeaderHtml);
			$objWriter->setHtmlWriterExtend($this->htmlWriterExtend);
			if($this->tableId!=null&&trim($this->tableId)!="")
			{
				$objWriter->setTableId($this->tableId);
			}
    		$objWriter->setSheetIndex(0);
    		return $objWriter;
   	 	}
   	 	public function getHtmlString()
   	 	{
   	 		$objWriter = $this->getHtmlWriter();
    		return $objWriter->getHtmlString();
   	 	}
   	}

 		
  

?>