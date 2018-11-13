<?php
namespace Quickplus\Lib;
	set_time_limit(0);
use Quickplus\Lib\QuickFormConfig;
	class QuickPdf 
	{   
		public static function createPdf($url,$file,$check=ture,$pageSize="Letter")
		{
			if($check)
			{
				if(is_file($file))
				{
					unlink($file);
				}
			}
			$command = QuickFormConfig::$quickPdfPath."wkhtmltopdf -s ".$pageSize." '".$url."' ".$file;
			shell_exec($command);
		}
	}


?>