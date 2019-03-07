<?php
namespace Quickplus\Lib;
require_once($_SERVER['DOCUMENT_ROOT']."/lib/PHPExcel.php");
set_time_limit(0);
use Quickplus\Lib\DataMsg\DataMsg;
use Quickplus\Lib\DataMsg\Data;
use Quickplus\Lib\Tools\FileTools;
use Quickplus\Lib\Tools\StringTools;
	class quickUploader
	{
		protected $cols = Array();
		protected $tables = Array();
		protected $commonData = Array();
		protected $commonCols = Array();
		protected $processCols = Array();
		protected $allowExtend = Array();
		protected $extensionSetting = null;
		protected $colPostion = Array();
		protected $process = null;
		protected $msg=null;
		protected $maxSize=2097152;
		protected $processMethod = "importFile";
		protected $fileName = null;
		protected $returnString = null;
        protected $startRow = 1;
        protected $startCol = 0;
        protected $fileExtCheck = true;
        protected $skipEmpty = true;

        public function setSkipEmpty($skipEmpty)
        {
        	$this->skipEmpty = $skipEmpty;
        }

        public function setFileExtCheck($fileExtCheck)
        {
        	$this->fileExtCheck = $fileExtCheck;
        }
        public function setStartCol($startCol)
        {
        	$this->startCol = $startCol - 1;
        }
        public function getStartCol()
        {
        	return $this->startCol;
        }
        public function setStartRow($startRow)
        {
        	$this->startRow = $startRow;
        }
        public function getStartRow()
        {
        	return $this->startRow;
        }
        public function getReturnString()
        {
        	return $this->returnString;
        }

	    public function setProcessMethod($processMethod)
	    {
	    	$this->processMethod = $processMethod;
	    }

		public function getFileName()
		{
			return $this->fileName;
		}

		public function setProcess($process)
		{
			$this->process = $process;
		}
		public function setMaxSize($maxSize)
		{
			$this->maxSize = $maxSize;
		}
		public function getMaxSize()
		{
			return $this->maxSize;
		}
		public function setMsg($msg)
		{
			$this->msg = $msg;
		}
		public function getMsg()
		{
			return $this->msg;
		}	

		public function upload($db,$src,$id,$path,$process=null,$mark=null)
        {    
        	 $this->init($db,$src,$id,$path);
        	 $process = $this->process;
        	 if($this->extensionSetting!=null)
        	 {
        	 	if($this->extensionSetting["override"])
        	 	{
        	 		$this->allowExtend =  Array();
        	 	}
        	 	if(is_array($this->extensionSetting["extension"]))
        	 	{
        	    	$this->allowExtend = array_merge($this->allowExtend,$this->extensionSetting["extension"]);
        	    }
        	 }
        	 $result  = $this->uploadFile($src,$id,$path,$mark);
        	 if($result&&$process)
        	 {

        	 	 $processMethod = $this->processMethod;

        	 	 $tmp =  $this->$processMethod($db,$src,$result,$this->returnString);
        	 	
        	 	 if(is_bool($tmp))
        	 	 {
        	 	 	$result = $tmp;
        	 	 }
        	 }
        	 return $result;
        }

        public function getHtmlFromWord($db,$src,$fileName)
        {
        	 $quickWord = new QuickWord($fileName);
        	 $this->returnString = $quickWord->getHtml();
        	 return true;
        }
        

        public function getContent($db,$src,$fileName)
        {
        	$this->returnString = file_get_contents($fileName);
        	return true;
        }
		public function setExtension($extension,$spiltBy=";",$override=false)
		{
			if(is_string($extension))
			{
				$extension =  explode($spiltBy,$extension); 
			}
			$this->extensionSetting = Array();
			$this->extensionSetting["extension"] = $extension;
			$this->extensionSetting["override"] = $override;

		}

		public function addAllowExtend($extends)
		{
			$this->allowExtend[]= $extends;
		}

		public function addTable($pk,$tablename,$key=null,$data=array())
        {
           if($key==null)
           {
               $key = $tablename;
           }
           $this->tables[trim($key)] = array("pk"=>trim($pk),"tablename"=>trim($tablename),"data"=>$data);
        }
        public function addCommonData($dbname,$realName,$value)
        {	
        	$this->commonData[$dbname] = $value;
        	$this->commonCols[$dbname] = $realName;
        }
        public function addCol($dbName,$realName,$postion=null,$jumpCol=false,$keepPostion=false)
        {
        	
        	$this->cols[$dbName] = $realName;
        	if($postion ==null)
        	{
        		$postion = intval($postion);
        		$this->postion[$dbName]["postion"] = $postion - 1;
        		$this->postion[$dbName]["jumpCol"] = $jumpCol;	
        	}
        	$this->postion[$dbname]["keepPostion"] = $keepPostion;
        	
        }

          public function addProcessCol($dbName,$realName)
        {
        	$this->processCols[$dbName] = $realName;
        	
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
        
        public function importFile($db,$src,$fullFileName,$filename)
        {
			$objPHPExcel = \PHPExcel_IOFactory::load($fullFileName);
			$sheetData = $objPHPExcel->getActiveSheet()->toArray(null,true,true,true);
			$dataMsg = new DataMsg();
			$dataMsg->setDb($db);
			if(count($sheetData)>0)
			{
				    $curRow = 1;
					$cols = array_merge($this->cols,$this->commonCols,$this->processCols);
					foreach($sheetData as $key => $value)
					{
						$load = $this->checkEmpty($value);
						if($curRow>=$this->getStartRow()&&$load)
						{
							$data = new Data($db);	
							$data->setCols($cols);
							$data->setTables($this->tables);
							$i = $this->getStartCol();
							foreach($this->commonData as $ckey => $cvalue)
							{
								$tmpvalue = $this->commonData[$ckey];
								$data->set($ckey,$tmpvalue);
							}
								
							$needUpdate = false;
							foreach($this->cols as $ckey=>$cvalue)
							{
								$point = $i;
								if(isset($this->colPostion[$ckey]["postion"])&&$this->colPostion[$ckey]["postion"]!=null&&$this->colPostion[$ckey]["postion"]!="")
								{
										$point = $this->colPostion[$ckey]["postion"];
										$jumpCol = $this->colPostion[$ckey]["jumpCol"];
										if($jumpCol)
										{
											$i = $point;
										}
								}

							
								if(isset($value[\PHPExcel_Cell::stringFromColumnIndex($point)]))
								{	
							   
									$tmpvalue = $value[\PHPExcel_Cell::stringFromColumnIndex($point)];
									$tmpvalue = iconv(mb_detect_encoding($tmpvalue), QuickFormConfig::$encode, $tmpvalue);
								}
								else
								{
									$tmpvalue = "";
								}
								if($tmpvalue!=null&&$tmpvalue!="")
								{
									$needUpdate = true;
								}
								$data->set($ckey,$tmpvalue);
								$keepPosition = false;
								if(isset($this->colPostion[$ckey]["keepPostion"])&&is_bool($this->colPostion[$ckey]["keepPostion"]))
								{
									$keepPosition = $this->colPostion[$ckey]["keepPostion"];
								}
								if(!$keepPosition)
								{
									$i++;
								}
							}
						    if($needUpdate)
						    {
								$data = $this->processData($db,$data,$src);
								$dataMsg->merge($data->getMtDataMsg());
							}
						}
						$curRow++;
					}
				

				if($dataMsg->getSize()>0)
				{
				
					$result = $dataMsg->batchCreateUpdate();
				    if($result)
				    {		
				    	$result = $this->afterImport($db,$dataMsg,$result);
				    }
				    else
				    {
				    	$this->setMsg("Database Operation Error ,please check.");
				    }
					return $result;
				}
			}
			return false;
        }
        public function afterImport($db,$dataMsg,$result)
        {
        	return $result;
        }
        public function processData($db,$data,$src)
        {	
        	return $data;
        }
       
		public function getExtension($filename)
		{
			$array = explode(".",$filename);
			$str = end($array);
			return strtolower($str);
		}
		public function getDstFileName($fileName,$mark=null)
		{
			$fileMark ="";
			if($mark!=null&&trim($mark)!="")
			{
				$fileMark =$mark;
			}
			return $fileMark.date('Ymdhis').StringTools::getRandStr().str_replace(" ", "", $fileName);
		}
		public function getFilenameWithoutExtension($filename)
		{
			$tmp_array = Array();
			$tmp_array = explode(".",$filename);
			
			return $tmp_array[0];
		}
        public function uploadFile($src,$id,$path,$mark=null)
        {
        	$result = false;
        	//echo $id."<br>";
        	//print_r($_FILES);
        	if(!isset($_FILES[$id]['name'])||$_FILES[$id]['name']==null&&trim($_FILES[$id]['name'])=="")
        	{
        		$this->msg = "Please choose a file at first.";
        		return $result;
        	}

			if($_FILES[$id]['error']===UPLOAD_ERR_OK)
			{
				$extension = $this->getExtension($_FILES[$id]['name']);
		     
				if(!in_array($extension,$this->allowExtend)&&$this->fileExtCheck)
				{
					
					$this->msg = "The extension '".$extension."' is not supported.";
					return $result;
				}

				if($_FILES[$id]['size']>$this->maxSize)
				{
					$this->msg = "File size can not exceed ".$this->maxSize.".";
					return $result;
				}
	        	$fileNameStr = explode('.',$_FILES[$id]['name']);
				$fileNameTmp = strtolower($fileNameStr[count($fileNameStr)-1]);
				$this->returnString = $this->getDstFileName($_FILES[$id]['name'],$mark);

			    FileTools::createDir($path);
			    $fileName = FileTools::connectPath($path,$this->returnString);
			 	
			    $srcFileName = iconv(mb_detect_encoding($_FILES[$id]['tmp_name']), QuickFormConfig::$encode,$_FILES[$id]['tmp_name']);
			   move_uploaded_file($srcFileName, $fileName);
		        $result = $fileName;
		  
			}
		    return $result;
        }

        public function init($db,$src,$id,$path)
        {
        	
        }


        
        public function buildInfo()
        {
        	$files = Array();
        
        	if($_FILES)
        	{
	        	$i=0;
	        	foreach($_FILES as $v)
	        	{
	        		//single file
	        		if(is_string($v['name']))
	        		{
	        			$files[$i]=$v;
	        			$i++;
	        		}
	        		else
	        		{
	        			//multi files
	        			foreach($v['name'] as $key=>$val)
	        			{
	        				$files[$i]['name']=$val;
	        				$files[$i]['size']=$v['size'][$key];
	        				$files[$i]['tmp_name']=$v['tmp_name'][$key];
	        				$files[$i]['error']=$v['error'][$key];
	        				$files[$i]['type']=$v['type'][$key];
	        				$i++;
	        			}
	        		}
	        	}
        	}

        	return $files;
        }
        
        //public function generalUploadFile($path="/upload",$allowExt=array("gif","jpeg","png","jpg","wbmp"),$maxSize=2097152,$imgFlag=true)
        public function generalUploadFile($path,$imgFlag=false)
        {
        	$uploadedFiles = Array();
        	
        	if(!file_exists($path))
        	{
        		mkdir($path,0777,true);
        	}
        	
        	$i=0;
        	$files=$this->buildInfo();
        	if( count($files)>0 )
        	{
	        	foreach($files as $file)
	        	{
	        		if($file['error']===UPLOAD_ERR_OK)
	        		{
	        			$ext=$this->getExtension($file['name']);
	        			//check file extension
	        			if(!in_array($ext,$this->allowExtend))
	        			{
	        				$this->msg = "File extension '".$ext."' is not supported.";
	        				$uploadedFiles = Array();
	        				break;
	        			}
	        			//is it really a image type file?
	        			if($imgFlag)
	        			{
	        				if(!getimagesize($file['tmp_name']))
	        				{
		        				$this->msg = "It is not a image format file.";
		        				$uploadedFiles = Array();
	        					break;
	        				}
	        			}
	        			//check fiel size
	        			if($file['size']>$this->maxSize)
	        			{
	        				$this->msg = "File size exceed the limit(".$this->maxSize.").";
	        				$uploadedFiles = Array();
							break;
	        			}
	        			//check upload file way is right?
	        			if(!is_uploaded_file($file['tmp_name']))
	        			{
	        				$this->msg = "It is not uploded as HTTP or POST method.";
	        				$uploadedFiles = Array();
							break;
	        			}
	        			$name=$this->getFilenameWithoutExtension($file['name']);
	        			$filename=$name."-".$this->getUniName().".".$ext;
	        			$destination=$path."/".$filename;
	        			if(move_uploaded_file($file['tmp_name'], $destination))
	        			{
	        				$file['name']=$filename;
	        				unset($file['tmp_name'],$file['size'],$file['type']);
	        				$uploadedFiles[$i]=$file;
	        				$i++;
	        			}
	        		}
	        		else
	        		{
	        			switch($file['error'])
	        			{
	        				case 1:
	        					$this->msg="Exceed the size that configure file allowed";//UPLOAD_ERR_INI_SIZE
	        					break;
	        				case 2:
	        					$this->msg="Exceed the size that form allowed";			//UPLOAD_ERR_FORM_SIZE
	        					break;
	        				case 3:
	        					$this->msg="File uploaded is not intact";//UPLOAD_ERR_PARTIAL
	        					break;
	        				case 4:
	        					$this->msg="No file upload";//UPLOAD_ERR_NO_FILE
	        					break;
	        				case 6:
	        					$this->msg="Can not find temporary dir";//UPLOAD_ERR_NO_TMP_DIR
	        					break;
	        				case 7:
	        					$this->msg="File is not writeable";//UPLOAD_ERR_CANT_WRITE;
	        					break;
	        				case 8:
	        					$this->msg="File uploading is interrupted by the extension program of PHP";//UPLOAD_ERR_EXTENSION
	        					break;
	        			}
        				$uploadedFiles = Array();
        				break;
	        		}
	        	}
        	}

        	return $uploadedFiles;
        }

        function getUniName()
        {
        	return md5(uniqid(microtime(true),true));
        }
        
	}
?>