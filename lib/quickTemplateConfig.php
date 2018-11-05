<?php 
   require_once(dirname(__FILE__)."/quickForm.php"); 
	class QuickTemplateConfig extends quickForm
	{
		protected $dbTableName = "qp_template";
		protected $methodClass = "";
		protected $methodClassSrc = "";
		public function getDbTableName()
		{
			return $this->dbTableName;
		}
	}
?>