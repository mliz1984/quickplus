<?php
namespace Quickplus\Lib;

 class QuickLoginManager
  {
  	  public static function getQuickLoginManager($src=null,$classname=null)
  	  {
  	  		if($src==null&&trim($src)=="")
  	  		{
  	  			$src = QuickFormConfig::$quickLoginManagerSrc;
  	  		}
  	  		if($classname==null&&trim($classname)=="")
  	  		{
  	  			$classname = QuickFormConfig::$quickLoginManagerClassname;
  	  		}
  	  		require_once($_SERVER['DOCUMENT_ROOT'].$src);
  	  		$result = new $classname();
  	  		return $result; 
  	  }
  }
?>
