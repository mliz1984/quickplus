<?php 
	    require_once(dirname(__FILE__)."/commonTools.php");
	    class QuickIni 
	    {
	    	 protected $iniData = Array();
	    	 protected $fileName = null;
	    	 function __construct($fileName,$isAbsolutePath=true)
	    	 {
	    	 	if($fileName!=null&&trim($fileName)!="")
	    	 	{
	    	 		$this->setFileName($fileName,$isAbsolutePath);
	    	 	}
	    	 }
	    	 public function getFileName()
	    	 {
	    	 	return  $this->fileName;
	    	 }
	    	 public function setFileName($fileName,$isAbsolutePath=true)
	    	 {
	    	 	if($fileName!=null&&trim($fileName)!="")
	    		{
	    			if(!$isAbsolutePath)
	    			{
	    				$fileName = FileTools::getRealPath($fileName);
	    			}
	    		}
	    		if(is_file($fileName))
	    		{
	    			$this->fileName = $fileName;
	    		}
	    	 }
	    	 public function load($fileName=null,$isAbsolutePath=true)
	    	 {	

	    	 	if($fileName!=null&&trim($fileName)!="")
	    		{
	    			if(!$isAbsolutePath)
	    			{
	    				$fileName = FileTools::getRealPath(trim($fileName));
	    			}
	    		}
	    		else if($this->fileName!=null&&trim($this->fileName)!="")
	    		{
	    			$fileName =  trim($this->fileName);
	    		}
	    		if(is_file($fileName))
	    		{
	    			$this->iniData = parse_ini_file($fileName,true);
	    		}
	    		return $this->iniData;
	    	 }

	    	 public function save($fileName=null,$isAbsolutePath=true)
	    	 {
	    	 	$result =false;
	    	 	$configFile = null;
	            if($fileName!=null&&trim($fileName)!="")
	            {
	            	$configFile = trim($fileName);
	            }
	            if($configFile!=null&&trim($configFile)!="")
	            {
	            	if(!$isAbsolutePath)
	            	{
	            		$fileName = FileTools::getRealPath(trim($fileName));
	            	}
	            }
	            else if($this->fileName!=null&&trim($this->fileName)!="")
	            {
	            	$configFile = trim($this->fileName);
	            }
	            if(is_array($this->iniData)&&count($this->iniData)>0)
	            {
		            $fh = fopen($configFile,"w");
		            foreach($this->iniData as $k =>$v)
		            {
		            	if(is_array($v))
		            	{
		            		$word = "[".$k."]\r\n";
		            		fwrite($fh, $word);
		            		foreach($v as $vk =>$vv)
		            		{
		            			$word =  $vk."=".$vv."\r\n";
		            		    fwrite($fh, $word);
		            		}
		            	}
		            	else
		            	{
		            		$word =  $k."=".$v;
		            		fwrite($fh, $word);
		            	}
		            }
		            fclose($fh);
					$result =true;
		        }   
		         return $result;
	    	 }

	    }
	  
?>
