<?php
namespace Quickplus\Lib;
require_once($_SERVER['DOCUMENT_ROOT']."/lib/PHPExcel.php");
require_once($_SERVER['DOCUMENT_ROOT']."/lib/parameters.php");
	set_time_limit(0);
    use Quickplus\Lib\QuickFormConfig as QuickFormConfig;
	use Quickplus\Lib\Tools\UrlTools;
	use Quickplus\Lib\Tools\FileTools as FileTools;
	use Quickplus\Lib\Tools\StringTools;
	class excelArray
	{
		protected $filePath = null;
		protected $keyColMapping = Array();
		protected $keyColFileMapping = Array();
		protected $startRow = 1;
		protected $endRow = null;
        protected $skipEmpty = true;

        public function setFilePath($filePath)
        {
        	$this->filePath = $filePath;
        }



        public function setSkipEmpty($skipEmpty)
        {
        	$this->skipEmpty = $skipEmpty;
        }

        public function __construct()
        {
        	 $this->init();
        }
        public function init()
        {

        }
		public function setStartRow($startRow)
		{
			$this->startRow = $startRow;
		}

		public function getStartRow()
		{
			return $this->startRow;
		}


		public function setEndRow($endRow)
		{
			$this->endRow = $endRow;
		}

		public function getEndRow()
		{
			return $this->endRow;
		}
        public function setKeyFile($key,$col)
        {
        	$this->keyColFileMapping[$key] = $col;
        }
		public function setKey($key,$col,$isNum=false,$allowEmptyValue=true,$defaultValue="")
		{

			$this->keyColMapping[$key] = Array("col"=>$col,"isNum"=>$isNum,"allowEmptyValue"=>$allowEmptyValue,"defaultValue"=>$defaultValue);
		}
		public function setKeyMethod($key,$customMethod)
		{
			$this->keyColMapping[$key]["customMethod"] = $customMethod;
		}
		public function afterLoad($result)
		{
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
		public function getArrayFromFile($fileName)
		{
			$objPHPExcel = \PHPExcel_IOFactory::load($fileName);
			$sheetData = $objPHPExcel->getActiveSheet()->toArray(null,true,true,true);
			$endRow = $this->getEndRow();
			$result = Array();	
			$curRow = 1;
			//echo "@@@";
			//print_r($sheetData);
			$mergeValueArray = Array();
			if(count($sheetData)>0)
			{
				foreach($sheetData as $key => $value)
				{

					$load = $this->checkEmpty($value);
					if($curRow>=$this->getStartRow()&&$load)
				    {	
				    	if($endRow!=null&&$currow>$endRow)
						{
							break;
						}
						$data = Array();
						foreach($this->keyColMapping as $key => $colInfo)
						{
							$col = $colInfo["col"];
							$y = $col;
							$isNum = $colInfo["isNum"];
							$customMethod = $colInfo["customMethod"];
							$allowEmptyValue = $colInfo["allowEmptyValue"];
							$defaultValue = $colInfo["defaultValue"];
							if($isNum)
							{
								$col = $PHPExcel_Cell::stringFromColumnIndex($col);
							}
							$keyval = $value[$col];
							if($customMethod!=null&&trim($customMethod)!="")
							{
								$keyval = $this->$customMethod($keyval,$value);
							}
							$addSign = true;
							if($keyval==null||(is_string($value)&&trim($keyval)=="")||(is_array($value)&&count($value)==0))
							{	
								$addSign = false;
								if($allowEmptyValue)
								{
									$addSign = true;
									$keyval = $defaultValue;
								}
							}
							$cell = $objPHPExcel->getActiveSheet()->getCell($y.$curRow);
							if($cell->isInMergeRange())
							{
								$mergeRange = $cell->getMergeRange();
								if($cell->isMergeRangeValueCell())
								{
								   $mergeValueArray[$mergeRange] = $keyval;
								}
								else
								{
									$keyval = $mergeValueArray[$mergeRange];
								}
							}

							if($addSign)
							{
								$keyval = StringTools::conv($keyval,QuickFormConfig::$encode);
								$data[$key] = $keyval;
							}
						}
						$result[] = $data;
				    }
				    $curRow ++;
				}
			}
			$fileResult = $this->loadFiles($objPHPExcel->getActiveSheet());
			if(count($fileResult)>0&&count($this->keyColFileMapping)>0)
			{
				$curRow =$this->getStartRow();
				$newResult = Array();
				foreach($result as $r)
				{
					foreach($this->keyColFileMapping as $key =>$col)
					{
						if(isset($fileResult[$curRow][$col])&&!empty($fileResult[$curRow][$col]))
						{
							$r[$key] = $fileResult[$curRow][$col];
						}
						else
						{
							$r[$key] = "";
						}
					}
					$newResult[] = $r;
					$curRow += 1;
				}
				$result = $newResult;
			}
			$result = $this->afterLoad($result);
			return $result;
		}
	

	public function loadFiles($sheet)
	{
		$imgData=array();
		if($this->filePath!=null&&count($this->keyColFileMapping)>0)
		{
			$data=$sheet->toArray();

			foreach($sheet->getDrawingCollection() as $img){
			    list ($startColumn, $startRow) = \PHPExcel_Cell::coordinateFromString($img->getCoordinates());//获取列与行号
			    $p_w_picpathFileName=$img->getCoordinates().mt_rand(100,999);
			    $p_w_picpathFileName.=".".$img->getExtension();
			    $filename = $img->getPath();
			    echo FileTools::connectPath(FileTools::getRealPath($this->filePath),$p_w_picpathFileName)."<br>";  
			    copy($filename, FileTools::connectPath(FileTools::getRealPath($this->filePath),$p_w_picpathFileName)); 
			    $imgData[$startRow][$startColumn]=$p_w_picpathFileName;
			     
			}
		}
		return $imgData;
	}
	}
?>