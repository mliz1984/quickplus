<?php
namespace Quickplus\Lib;
use Quickplus\Lib\quickForm as quickForm;
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