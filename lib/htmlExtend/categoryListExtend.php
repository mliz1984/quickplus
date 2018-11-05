<?php
   class categoryListExtend
   {
        public static function levelSearch($level,$array)
   	    {
   	        if($level==0)
	     	{ 
		 		$temp = Array();
		 		$temp [] = Array("id"=>"0","name"=>"Top Level");
				$array = array_merge($temp,$array);
	     	}
   	        return $array;
   	    }
   }
?> 