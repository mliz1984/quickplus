<?php 
    set_time_limit(0);
    require_once(dirname(__FILE__)."/quickUploader.php");
    require_once(dirname(__FILE__)."/commonTools.php");

    class quickBatchFileProcesser extends quickUploader
    {
    	protected $srcPath = null;
    	protected $processPath = null;
    	protected $backupFile  = null;
        protected $backupAsZip = false;
        public function setBackUpAsZip($backupAsZip)
        {
            $this->backupAsZip = $backupAsZip;
        }
    	public function setSrcPath($path)
    	{
            if(!StringTools::isEndWith($path,"/"))
            {
                $path.="/";
            }       
    		$this->srcPath = $path;
    	}

    	public function setProcessPath($path)
    	{
            if(!StringTools::isEndWith($path,"/"))
            {
                $path.="/";
            }
    		$this->processPath = $path;
    	}

    	public function setBackupFile($filename)
    	{
           
    		$this->backupFile = $filename;
    	}

    	public function beforeBatchProcess($db,$src,$fileList)
    	{

    	}

    	public function afterBatchProcess($db,$src,$fileList,$result)
    	{
    		
    	}
    	

    	public function batchProcess($db,$src)
    	{


    		 if($this->process!=null)
             {
             	$process = $this->process;
             }
        	 $this->init($db,$src,$id,$path);
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
        	 $workPath = $this->srcPath;
            
        	 if($this->processPath!=null&&trim($this->processPath)!=""&&$workPath != $this->processPath)
        	 {
                  
        	 	   FileTools::moveDir($workPath,$this->processPath,true,false);
                   FileTools::unlinkDir($workPath,false);
                   $workPath = $this->processPath;

        	 }

        	 $fileList = FileTools::getFileList($workPath,false,true,$this->allowExtend);
                 
        	 $this->beforeBatchProcess($db,$src,$fileList);
        	 $result = Array();
        	 if(count($fileList)>0)
        	 {
        	 	foreach($fileList as $file)
        	 	{
        	 		 $processMethod = $this->processMethod;
	        	 	 $tmp =  $this->$processMethod($db,$src,$this->processPath.$file);
	        	 	 if($tmp!=null&&is_bool($tmp))
	        	 	 {
	        	 	 	 $result[$file] = $tmp;
	        	 	 }
        	 	}
        	 }
        	 $this->afterBatchProcess($db,$src,$fileList,$result);	 
        	 if($this->backupFile!=null)
        	 {
                if($this->backupAsZip)
                {
        	 	   FileTools::zip($this->backupFile,$workPath,$workPath);  
                }
                else
                {
                    FileTools::copyDir($workPath,$this->backupFile);
                }     
        	 }
            FileTools::unlinkDir($workPath,false);
        	   
    	}

    }
?>