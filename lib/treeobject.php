<?php
 namespace Quickplus\Lib;
 class TreeObject{
        protected $idkey;
        protected $parentKey;
        protected $data;
        protected $tree;
        protected $topSign;
        protected $topNodeArray = Array();
        protected $topNodeIdArray = Array();
        public  function __construct($data,$idkey,$parentKey,$topSign="top")
        {       

           $this->idkey = $idkey;
           $this->parentKey = $parentKey;
           $this->topSign = $topSign;
           $this->data = Array();

           for($i=0;$i<count($data);$i++)
           {
               $value = $data[$i][$this->idkey];
               $this->data[$value]= $data[$i];

           } 
          
        }
        public function getTopNodeIdArray()
        {
            return $this->topNodeIdArray;
        }
        public function getTopNodeArray()
        {
            return $this->topNodeArray;
        }
        public function getDataValue($id)
        {
            $result = null;
            if(isset($this->data[$id]))
            {
               $tmp = $this->data[$id];
               if($tmp!=null&&trim($tmp)!="")
               {
                    $result = $tmp;
               }
            }
            return $result;
        }
        public  function setTopSign($topSign)
        {
             $this->topSign = $topSign;
        }
        public function getTopSign()
        {
            return  $this->topSign;
        }
        
        public  function setIdkey($idkey)
        {
             $this->idkey = $idkey;
        }
        public  function setParentKey($parentKey)
        {
             $this->parentKey = $parentKey;
        }
        public  function setData($data)
        {
             $this->data = $data;
        }
        
        public function buildTree()
        { 
           foreach($this->data as $key => $value)
           {

               $parentValue = $value[$this->parentKey];
               if($parentValue!=null&&trim($parentValue)!=""&&$parentValue!=$this->getTopSign)
               {
                    $this->tree[$key] = $parentValue;    
               }
               else {
                   $this->tree[$key] =  $this->topSign;
                   $this->topNodeArray[] = $value;
                   $this->topNodeIdArray[] = $value[$this->idkey];
               }   
           } 
        
        }

         public function getParent($id,$isValue=true)
         {
             $value = $this->tree[$id];
             if($value==$this->topSign)
             {
                 return null;
             }
             if($isValue)
             {
                 return $this->data[$value];
             }
             return $value;  
         }
         public function hasParent($id)
         {
             $value = $this->getParent($id,false);
             if(strval($value)==strval($this->topSign)||$value==null||!isset($this->tree[$value])||$id==$value)
             {
                 return false;
             }
             return true; 
         }

         public function getTopNode($id,$isValue=true)
         {
             $value = $this->getParent($id);
             if($value==$this->topSign||$value == $id)
             {
                $value = $id;   
             }
             else
             {
                $value = $this->getTopNode($value,false);
             }
             if($isValue)
             {
                 $value = $this->data[$value];
             }
             return $value;
         }
         
         
         public function getPath($id,$isValue=true,$withSelf=true)
         {
             $result = Array();
             if($id!=null&&$id!="")
             {
                 if($withSelf)
                 {
                     if($isValue)
                     {
                         $result[] = $this->data[$id];
                     }
                     else {
                         $result[] = $id;
                     }
                 }
                while($this->hasParent($id))
                {
                    $id = $this->getParent($id,false);
                    if($isValue)
                    {
                         $result[] = $this->data[$id];
                    }
                    else {
                         $result[] = $id;
                     }
                }
             }
            return $result;
         }
         public function getDepth($id)
         {
           return count($this->getPath($id,false));
         }
         public function hasChild($id)
         {
             foreach($this->tree as $key=>$value)
             {
                 if($value==$id&&$key!=$value)
                 {
                     return true;
                 }
             }
             return false;
         }
        
         
         public function getChild($id,$isValue=true,$fulltree=true,$withself=true)
         {
        
            if($withself)
            {
                 if($isValue)
                 {
                     $result[] = $this->data[$id];
                 }
                 else {
                     $result[] = $this->id;
                 }
            }
              foreach($this->tree as $key=>$value)
              {
                 if($value==$id)
                 {
                     if($isValue)
                     {
                         $result[] = $this->data[$key];
                     }
                     else {
                         $result[] = $key;
                     }
                   if($fulltree)
                   {
                     
                         if($this->hasChild($key))
                         {
                           $result = array_merge($result,$this->getChild($key,$isValue,$fulltree,false));
                         }
                   }
                 }
              }
              return $result;
         }         
} 

 
class TreeVo extends TreeObject
{
        public  function __construct($topSign="top")
        {        
           $this->idkey = "id";
           $this->parentKey = "parent";
           $this->topSign = $topSign;
           $this->data = Array();
        }

        public function addTop($id)
        {
             $this->add($id, $this->topSign);   
        }
        public function add($id,$value)
        {
             $tmp = Array();
             $tmp[$this->idkey] = $id;
             $tmp[$this->parentKey] = $value;
             $this->data[$id]= $tmp;
        }

        public function addMap($data,$idkey,$parentkey)
        {
            $this->add($data[$idkey],$data[$parentkey]);
        }

        public function batchAdd($data,$mapmode = false,$idkey=null,$valkey=null)
        {
        
           foreach($data as $id=>$value)
           {
               $this->add($id,$value);
           }
        }

        public function  batchAddMapArray($data,$idkey,$valkey)
        {
            foreach($data as $item)
            {
                $this->addMap($item,$idkey,$valkey);
            }
        }
}