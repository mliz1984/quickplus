<?php 
 class BaseVo
 {
      
      protected $dataArray;
      protected $tolower = true;
      protected $isPublic = true;
      function __construct($tolower = true,$isPublic=true)
      {
      	$this->dataArray = array();
      	$this->tolower = $tolower;
      	$this->isPublic = $isPublic;
      }
      function __set($name, $value)
      {     
      	 if($this->isPublic)
      	 {
      	   if($this->tolower)
      	   {
      	   	   $name = strtolower($name);
      	   }
           $this->dataArray[$name] = $value;
         }
      }    
      function __get($name)
      {  
      	if($this->isPublic)
      	{
      	   if($this->tolower)
      	   {
      	   	   $name = strtolower($name);
      	   }
      	   return $this->dataArray[$name];
      	}
      }

      public function setDataArray($dataArray)
      {
          $this->dataArray = $dataArray;
      }

      public function getDataArray()
      {
            $result = $this->dataArray;
            if(!is_array($result))
            {
                 $result = array();
            }
            return $result;
      }
       
      public function remove($name)
      {
      	unset($this->dataArray[$name]);
      }

      public function setTolower($tolower = true)
      {
      	$this->tolower = $tolower;
      }

      public function setPublic($isPublic = false)
      {
      	  $this->isPublic = $isPublic;
      }

      public function get($name,$defaultValue="")
      {
          $value = $this->dataArray[$name];
          if($value==null)
          {
              return $defaultValue;
          }
          return $value;
      }

      public function set($name,$value)
      {
         $this->dataArray[$name] = $value;
      }
      public function clear()
      {
      		$this->dataArray = array();
      }

    
      
     public function delete($name,$i,$length=1)
     {
     	  $list = $this->get($name,array());
     	  if(count($list)>=$i)
     	  {
     	  	 array_splice($list,1,$length);  
     	  }
     	  $this->set($name,$list);
     }

     public function count($name)
     {
     	 $list = $this->get($name,array());	 
     	 return count($list);
     }

      public function __call($name,$arguments)
      {
         $col = "";
         $array = Array("is","set","get","remove","add","clear","delete","count");
         $type6 = substr($name,0,6);
         $type5 = substr($name,0,5);
         $type3 = substr($name,0,3); 
         $type2 = substr($name,0,2);
         $type = "";
         if(in_array($type6,$array))
         {
            $type = $type6;
         }
         if(in_array($type5,$array))
         {
            $type = $type5;
         }
         else if(in_array($type3,$array))
         {
  			   $type = $type3;
         }
         else if(in_array($type2,$array))
         {
  			   $type = $type2;
         }

         if(in_array($type,$array))
         {
         	$col = substr($name,strlen($type));
         	if($this->tolower)
         	{
         		$col = strtolower($col);
         	}
         	if($type=="set"||$type=="count")
         	{
         	   $this->$type($col,$arguments[0]);
         	}
           	elseif($type=="get")
           	{
           		return $this->get($col);
           	}
           	elseif($type=="delete")
           	{
           	    $this->delete($col,$arguments[0],$arguments[1]);
           	} 
           	elseif($type=="add")
           	{
           		$agrs = $arguments;
           	    if($col==null&&trim($col)=="")
           	    { 
                   $col =  $arguments[0];
                   array_splice($agrs,0,1);      
           	    }
           	    $tmp =  $this->get($col);
           		if(is_array($tmp))
           		{
           			$tmp =  array_merge($tmp,$agrs); 
           			$this->set($col,$tmp);
           		}
           		else
           		{
           			$this->set($col,$arguments);
           		}
           	}
           	elseif($type=="clear")
           	{
           		$this->set($col,array());
           	}
           	elseif($type=="is")
            {
               $tmp =  $this->get($col);
    
               if(intval($tmp)>0)
               {
               		return true;
               }
               else
               {
               		return false;
               }
            }
            elseif($type=="remove")
            {
            	$this->remove($col);
            }
         }
         else
         {
         	throw new Exception("The VO can't support function ".$name);
         }
      }
 } 
?>