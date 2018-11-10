<?php 
	 require_once(dirname(__FILE__) . "/commonTools.php");
	 require_once(dirname(__FILE__)."/quickIni.php");
	class QuickLanguage
	{
		protected $languagePath = Array();
		protected $languageList = Array();
		protected $languageData = null;
		protected $defaultLanguageCode = null;
		protected $defaultLanguageName = null;
		protected $currentLanguageCode = null;
		protected $defaultLanguageCategory = "_quick_language_default_category";
		public function addLanguage($languageCode,$languageName)
		{
			$this->languageList[$languageCode] = $languageName;
		}
		public function setDefaultLanguage($languageCode,$languageName)
		{
			$this->defaultLanguageCode = $languageCode;
			$this->defaultLanguageName = $languageName;
		}
		public function getDefaultLanguage()
		{
			return Array("code"=>$this->defaultLanguageCode,"name"=>$this->defaultLanguageName);
		}

		public function setLanguagePath($path,$category=null,$isAbsolutePath=false)
		{
			$result = false;
			$cate = $this->defaultLanguageCategory;
			if($category!=null&&trim($category)!="")
			{
				$cate = $category;
			}
			if($path!=null&&trim($path)!="")
			{
				if(!$isAbsolutePath)
				{
					$path = FileTools::getRealPath($path);
				}
				if(is_dir($path))
				{
					$this->languagePath[$cate] = $path;	
					$result = true;
				}
			}
			$this->languageData = null;
			return $result;
		}

		public function getLanguageString($cate,$part,$key,$languageCode=null,$defaultValue="")
		{
			if($languageCode==null||trim($languageCode)=="")
			{
				$languageCode = $this->getUserFullLanguage();
			}
			$languageCode =  $this->getLanguage($languageCode);
			$this->languageData = Array(); 
			if(strtolower(trim($languageCode))!=strtolower(trim($this->currentLanguageCode)))
			{
				$this->loadLanguageData($languageCode);
			}
			$result = $defaultValue;
			if($this->languageData[$cate][$part][$key]!=null&&trim($this->languageData[$cate][$part][$key])!="")
			{
				$result = $this->languageData[$cate][$part][$key];
			}
			return $result;
		}


		public  function loadLanguageData($languageCode=null)
		{
			$this->currentLanguageCode = $this->getLanguage($userLanguage);
			$this->languageData = Array();
			foreach($this->languagePath as $cate => $path)
			{
				$file = FileTools::connectPath($path,$languageCode.".ini");
				if(is_file($file))
				{
					$quickIni = new quickIni($file);
					$this->languageData[$cate] = $quickIni->load();
				}
			}
		}
		public function __construct($fileName=null,$isAbsolutePath=true)
		{
			$configFile = "quickLanguage.ini";
			if($fileName!=null&&trim($fileName)!="")
			{
				if(!$isAbsolutePath)
				{
					$fileName = FileTools::getRealPath($fileName);
				}
				$configFile = $fileName;
			}
			if(file_exists($configFile))
			{
				$quickIni = new QuickIni($configFile);
				$iniData = $quickIni->load();
				$this->defaultLanguageCode = $iniData["DefaultLanguage"]["code"];
				$this->defaultLanguageName = $iniData["DefaultLanguage"]["name"];
				$this->languageList = $iniData["LanguageList"];
			}
		}	

 		public function getUserFullLanguage()
 		{
 			$array = explode(",",$_SERVER['HTTP_ACCEPT_LANGUAGE']);
 			return $array[0];
 		}

 		public function getUserLanguage()
 		{
 			$fullLanguage = $this->getUserFullLanguage();
 			$tmp = explode("-",$fullLanguage);
 			return $tmp[0];
 		}

 		public function getUserCountryCode()
 		{
 			$fullLanguage = $this->getUserFullLanguage();
 			$tmp = explode("-",$fullLanguage);
 			return $tmp[1];
 		}




 		public function getLanguage($userLanguage=null)
 		{
 			$result = null;
 			if($userLanguage==null&&trim($userLanguage)=="")
 			{
 				$userLanguage = $this->getUserFullLanguage();
 			}
 			foreach($this->languageList as $code => $name)
 			{
 				if(strtolower(trim($code))==strtolower(trim($userLanguage)))
 				{
 					$result = $code;
 					break;
 				}
 			}
 			if($result == null ||trim($result) == "")
 			{
 				$userLanguage = $this->getUserLanguage();
 				if(strtolower(trim($code))==strtolower(trim($fullLanguage)))
 				{
 					$result = $code;
 					break;
 				}
 			}
 			if($result == null ||trim($result) == "")
 			{
 				$result = $this->defaultLanguageCode;
 			}

 			return $result;
 		}

	}
?>