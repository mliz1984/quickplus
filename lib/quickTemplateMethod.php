<?php 
	class QuickTemplateMethod
	{
		protected $methodList = Array();
	    protected $methodType = Array();
		protected $debug = false;
		public function isDebug()
		{
			return $this->debug;
		}
		public function setDebug($debug)
		{
			$this->debug = $debug;
		}
		public function getMethodSize()
		{
			return count($this->methodList);
		}

		public function debugQuickTemplate($db,$colvalue,$title,$content,$dataArray)
		{
			$array = Array("colvalue"=>$colvalue,"title"=>$title,"content"=>$content,"dataArray"=>$dataArray);
			echo "<br>";
			print_r($array);
		}
		public function addMethod($type,$name,$method)
		{
			$this->methodList[$method] = $name;
			$this->methodType[$method] = $type;
		}

		public function getMethodType($method)
		{
			return $this->methodType[$method];
		}

		public function getMethodList()
		{
			return $this->methodList;	
		}
        public function init()
        {
        	
        }
		function __construct()
		{
			$this->init();
		}
		
	}
?>