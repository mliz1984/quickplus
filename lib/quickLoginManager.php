<?php
namespace Quickplus\Lib;
use \Quickplus\Lib as Quickform;
 class QuickLoginManager
  {
  	  public static function getQuickLoginManager($src=null,$classname=null)
  	  {
  	  		if($src==null&&trim($src)=="")
  	  		{
  	  			$src = Quickform\QuickFormConfig::$quickLoginManagerSrc;
  	  		}
  	  		if($classname==null&&trim($classname)=="")
  	  		{
  	  			$classname = Quickform\QuickFormConfig::$quickLoginManagerClassname;
  	  		}
  	  		require_once($_SERVER['DOCUMENT_ROOT'].$src);
  	  		$result = new $classname();
  	  		return $result; 
  	  }
  }
?>
