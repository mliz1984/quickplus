<?php
namespace Quickplus\Lib;
     class QuickUrl{
      	public static function post($url,$data)
      	{
      		    $context = array();
			    $context['http'] = array(
			        'method' => 'POST',
			        'header'  => 'Content-type: application/x-www-form-urlencoded',
			        'content' => http_build_query($data)
			    );
			    return file_get_contents($url, false, stream_context_create($context));
      	}
 		public static function forwardTo($url)
 		{
		    if($_SERVER['REQUEST_METHOD']=='GET'){
		    	  
		    	   Header("Location: ".$url); 
		    }
			else{
			   
			    $html = self::post($url,$_POST);
			    echo $html;
			}
 		}			
	}
 ?>