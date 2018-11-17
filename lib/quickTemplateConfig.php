<?php
namespace Quickplus\Lib;
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