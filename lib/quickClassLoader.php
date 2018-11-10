<?php
    require_once(dirname(__FILE__) . "/UrlTools.php");
    class QuickClassLoader
    {
    	protected $classLoader = "quickClassLoader";
    	protected $classList = Array();
        
        function __autoload($className)
        {
        	$this->$classLoader($className);
        }

    	public function setClassLoader($classLoader)
    	{
    		$this->classLoader = $classLoader;
    	}

    	public function getClassInfo($className=null,$src=null)
    	{
    		 $result = null;
    		
    		 if(is_string($this->classList[$className])||is_array($this->classList[$className]))
    		 {
    		 	$result = $this->classList[$className];
    		 }
    		 else 
    		 {
    		     if(class_exists($className))
    		     {
    		     	  $src = FileTools::getClassFilePath($className);
    		     	  $result = $this->getClassInfoArray($className,$src);
    		     }
    		     else
    		     {
    		     	 if($src!=null&&trim($src)!="")
    		     	 {
    		     	 	 try
    		     	 	 {
    		     	 	 	include_once($src);
    		     	 	 	if(class_exists($className))
    		     	 	 	{
    		     	 	 		 $result = $this->getClassInfoArray($className,$src);
    		     	 	 	}
    		     	 	 }		
    		     	 	 catch(Exception $e)
						 {
						 	$result = null;
						 }
    		     	 }
    		     }
    		 }
    		 return $result;
    	}

    	protected function getClassInfoArray($className,$src,$extendInfo = null)
    	{
            $result = Array();
            $result["calssName"] = $className;
            $result["src"] = $src;
            if(is_array($extendInfo))
            {
                foreach($extendInfo as $key=>$value)
                {
                    $result[$key]  = $value;
                }
            }
            return $result;
    	}


    	public function getClass($className,$src=null)
    	{
    		return $this->$classLoader($className,$src);
    	}

    	public function setClass($className,$src,$extendInfo=null)
    	{    
    		$this->classList[$className] = $this->getClassInfoArray($className,$src,$extendInfo);
    	}

        public function quickClassLoader($name,$className=null,$src=null)
        {
        	$result = false;
        	$classInfo = $this->getClassInfo($name,$className,$src);
        	if(is_array($classInfo))
        	{
        		$false = true;
        	}
            else
            {
            	 throw new Exception("Sorry, We can't load Class ".$className." Please give check the config or give us a new src.");
            }
        	return $result;
        }
       
        public function loadClassList($src,$className,$parameterName)
        {
        	try{
        			include_once($src);
        			if(class_exists($className))
        			{
        				$array = $className::$parameterName;
        				if(is_array($array))
        				{
        					foreach($array as $name =>$info)
        					{
        						$this->classList[$name] = $info; 
        					}
        				}
        			}
        	   }
        }

    
    }
?>