<?php
namespace Quickplus\Lib;
use \Quickplus\Lib\Tools\HtmlElement as HtmlElement;

	class QuickMenu
    {
        protected $data = Array();
		protected $categroyData = Array();
		protected $allowEmptyTopSign = true;
		protected $topData = Array();
		protected $topSign = Array();
        protected $nameMapping = Array();
        protected $defaultTarget = null;
        protected $keySetting = Array();
        protected $keyList = Array();
        protected $defaultPramas = Array();
        protected $showEmptyCategory = false;
        protected $lasturlsession = null; 
        public function setLastUrlSession($session)
        {
        	$this->lasturlsession = $session;
        }
        public function setShowEmptyCategory($showEmptyCategory)
        {
        	$this->showEmptyCategory = $showEmptyCategory;
        }
        public function addTopSign($topSign)
        {
        	if(!in_array($topSign, $this->topSign))
        	{
        		$this->topSign[] = $topSign;
        	}
        }
        public function addCategory($id,$parentId=null,$name=null)
        {
        	if($name==null&&trim($name)=="")
        	{
        		$name = $id;
        	}
            $isTopItem =  $this->isTopItem($parentId);
            if($isTopItem)
            {

            	$this->addTopData($id,$name,true,null,null);
            }
            else
            {
        		$this->categroyData[$parentId][$id] = $name;
        	}
        }


        public function addCategoryArray($dataArray,$idKey,$parentIdKey,$nameKey=null)
        {
       			$id = $dataArray[$idKey];
       			$parentId = $dataArray[$parentIdKey];
       			$name = null;
       			if($nameKey!=null&&trim($nameKey)!="")
       			{
       				$name =  $dataArray[$nameKey];
       			}
       			$this->addCategory($id,$parentId,$name);
       	}

       	public function addCategoryList($dataList,$idKey,$parentIdKey,$nameKey=null)
       	{
       		foreach($dataList as $dataArray)
       		{
       			$this->addCategoryArray($dataArray,$idKey,$parentIdKey,$nameKey);
       		}
       	}

        public function addTopData($id,$text,$isCategory,$url,$target,$data=null)
        {
        	$pre = "i_";
            if($isCategory)
            {
            	$pre  = "c_";
            }
            $array = Array("id"=>$id,"text"=>$text,"isCategory"=>$isCategory,"url"=>$url,"target"=>$target,"data"=>$data);
            $this->topData[$pre.$id] = $array;
        }
      
        public function getCategoryName($categoryId)
        {
        	$result = strval($categoryId);
        	if($this->categoryNameMapping[$categoryId]!=null&&trim($this->categoryNameMapping[$categoryId])!="")
        	{
        		$result = $this->categoryNameMapping[$categoryId];
        	}
        	return $result;
        }

        public function setDefaultPrama($key,$value,$fromFunc=false,$method=null)
        {
        	
        	$this->defaultPramas[$key] = $value;
        	
        	$this->setKeySetting($key,$fromFunc,$method);
        }
        public function setKey($key,$fromFunc=false,$method=null,$fromKey=null)
        {
        	if($fromKey==null||trim($fromKey)=="")
        	{
        		$fromKey = $key;
        	}
            $this->keyList[$key] = $fromKey;
        	$this->setKeySetting($key,$fromFunc,$method);
        }

        public function getKeySetting($key)
        {
        	$result = Array("fromFunc"=>false,"method"=>null);
        	if(!empty($this->keySetting[$key]) && is_array($this->keySetting[$key]))
        	{
                $result = $this->keySetting[$key];
            }
        	return $result;
        }
     
        public function setKeySetting($key,$fromFunc=false,$method=null)
        {
        	$this->keySetting[$key] = Array("fromFunc"=>$fromFunc,"method"=>$method);
        }
        public function setNameMapping($key,$nameMapping)
        {
        	$this->nameMapping[$key] = $nameMapping;
        }
        public function setDefaultTarget($defaultTarget)
        {
        	$this->defaultTarget = $defaultTarget;
        }
	

		public function setAllowEmptyTopSign($allowEmptyTopSign)
		{
			$this->allowEmptyTopSign = $allowEmptyTopSign;
		}

	    public function hasChild($categoryid)
	    {
	    	$result = false;

	    	if(isset($this->categroyMapping[$categoryid])&&is_array($this->categroyMapping[$categoryid])&&count($this->categroyMapping[$categoryid])>0)
	    	{
	    		$result = true;
	    	}
	    	if(!$result&&is_array($this->data[$categoryid])&&count($this->data[$categoryid])>0)
	    	{
	    		$result = true;
	    	}
	    	return $result;
	    }


	    public function addData($id,$categoryid,$text,$url=null,$target=null,$extendParams = null)
	    {
	    	if($target==null&&trim($target)==null)
	    	{
	    		$target = $defaultTarget;
	    	}
	    	$data = $extendParams;
	    	if(!is_array($data))
	    	{
	    		$data = Array();
	    	}
	    	$data["id"] = $id;
	    	$data["categoryid"] = $categoryid;
	    	$data["text"] = $text;
	    	$data["url"] = $url;
	    	$data["target"] = $target;
	    	//echo "@@".$target;
	    	//print_r($data);
	    	$this->addDataArray($data,"id","categoryid","text","url","target",$extendParams);
	    	
	    }

	    public function addDataList($dataList,$idkey,$categoryKey,$textKey,$urlKey,$targetKey)
	    {
	     	foreach($dataList as $data)
	     	{
	     		$this->addDataArray($data,$idkey,$categoryKey,$textKey,$urlKey,$targetKey);
	     	}
	    }
        public function isTopItem($categoryId)
        {
        	$isTopSign = false;
	    	
	    	if($categoryId!=null&&trim($categoryId)!="")
	    	{
	    		if(is_array($this->topSign)&&in_array($categoryId, $this->topSign))
	    		{
	    			$isTopSign = true;
	    		}
	    	}
	    	elseif($this->allowEmptyTopSign)
	    	{
	    		$isTopSign = true;
	    	}

	    	return $isTopSign;
        }
	    public function isTopCategory($categoryId)
	    {
	    	$isTopSign = false;
	    	
	    	if($categoryId!=null&&trim($categoryId)!="")
	    	{
	    		if(is_array($this->topSign)&&in_array($categoryId, $this->topSign))
	    		{
	    			$isTopSign = true;
	    		}
	    		if(!$isTopSign&&is_array($this->topData["c_".$categoryId]))
	    		{
	    			$isTopSign = true;
	    		}
	    	}
	    	elseif($this->allowEmptyTopSign)
	    	{
	    		$isTopSign = true;
	    	}

	    	return $isTopSign;
	    }
      
		public function addDataArray($data,$idKey,$categoryKey,$textKey,$urlKey=null,$targetKey=null,$extendParams = null)
		{
			$array = array();

			$keyList =Array("id"=>$idKey,"categoryid"=>$categoryKey,"text"=>$textKey,"url"=>$urlKey,"target"=>$targetKey);

			foreach($keyList as $key=>$fromKey)
			{
			
				$keySetting = $this->getKeySetting($key);

				$fromFunc = $keySetting["fromFunc"];
		        $method = $keySetting["method"];				
		        $this->setKey($key,$fromFunc,$method,$fromKey);
			}
			foreach($this->keyList as $key=>$fromKey)
	    	{
	    		$value = $data[$fromKey];
	    		$keySetting = $this->getKeySetting($key);
				$fromFunc = $keySetting["fromFunc"];
		        $method = $keySetting["method"];		
	    		if($fromFunc&&$method!=null&&trim($method)!="")
	    		{
	    			$method = trim($this->keySetting[$key]["method"]);
	    			$value = $this->$method($data);
	    		}
	    		if(!empty($this->nameMapping[$key][$value]))
	    		{
	    			$value = $this->nameMapping[$key][$value];
	    		}
	    		$array[$key] = $value;
	    	}
	    	
	    	$categoryId = $array["categoryid"];

	    	$isTopSign = $this->isTopItem($categoryId);
	    	if($isTopSign)
	    	{
	    		$this->addTopData($array["id"],$array["text"],false,$array["url"],$array["target"],$array);
	    	}
	    	else
	    	{
	        	$this->data[$categoryId][$array["id"]] = $array;
	    	
	    	}
	    	//echo $categoryId;
	    	//print_r($data);
	    	//echo "<br><br>";

		}

		public function getHtml($id)
		{
			$html ='<div>';
			
			if(count($this->topData)>0)
			{
				$class = "nav nav-pills nav-stacked left-menu";
				
			    $html.= "<ul class='".$class."' id='".$id."'>";

			    foreach($this->topData as $id => $info)
			    {

			    	$html.="<li style='list-style-type:none'>";
			    	$isCategory = $info["isCategory"];  
			 
                    if(!$isCategory)
			    	{
                       $data = $info["data"];
                       $html.=$this->getItemUrl($data);
			    	}
			    	else
			    	{

			    		$html.= $this->getCategoryUrl($info["id"],$info["text"],$id,$id);
			    	}
			    	$html.="</li>";
			    }
			    $html.= "</ui>";
			    $html.="</div>";
			    return $html;
			}
		}

		protected function getCategoryUrl($categoryId,$text,$id,$topid)
		{
			 $html = "";
			 
			 $hasChild = $this->hasChild($categoryId);
			 if($this->showEmptyCategory||$hasChild)
			 {
			 	$html .='<a  href="#"  data-target="#'.$topid.'_child_'.$categoryId.'" data-toggle="collapse" data-parent="#'.$id.'">'.$text.'</a>';
			 	if($hasChild)
			 	{
					$html .= '<ul class="nav-stacked collapse left-submenu" id="'.$topid.'_child_'.$categoryId.'">'; 
					if(isset($this->data[$categoryId]))
					{
					 	foreach($this->data[$categoryId] as $id => $data)
					 	{

					 		$html.="<li style='list-style-type:none'>".$this->getItemUrl($data)."</li>";
					 	}
					 }
				 	if(isset($this->categroyData[$categoryId]))
				 	{
					 	foreach($this->categroyData[$categoryId] as $id => $name)
					 	{
					 		$html.="<li style='list-style-type:none'>".$this->getCategoryUrl($id,$name,$categoryId,$topid)."</li>";
					 	}
					 }
				 	$html .='</ul>';
				}
			 }
			 return $html;
		}

		protected function getItemUrl($data)
		{
			           $urlElement = new HtmlElement(); 
                      $skipKeyArray = Array("id","categoryid","text","url","target");
                       foreach($data as $key => $value)
                       {
                       		if(!in_array($key, $skipKeyArray))
                       		{
                       			$urlElement->setParam($key,$value);
                       		}
                       }
                     
                       foreach($this->defaultPramas as $key => $value)
                       {

                       		$keySetting = $this->getKeySetting($key);
                       		$fromFunc = $keySetting["fromFunc"];
					        $method = $keySetting["method"];		
				    		if($fromFunc&&$method!=null&&trim($method)!="")
				    		{
				    			$method = trim($this->keySetting[$key]["method"]);
				    			$value = $this->$method($data);
				    		}
				    		$urlElement->setParam($key,$value);
                       }
                       $params = $urlElement->getParams();
				       $text = $data["text"];
				       $url = $data["url"];
				       $target = $data["target"];
				       if($this->lasturlsession!=null&&trim($this->lasturlsession)!=null)
				       {
				       		$sessionKey = $this->lasturlsession;
				       		$js =   "$.session.set('".$sessionKey."', '".$url."');";
				       		if(isset($params["onClick"])&&$params["onClick"]!=null&&trim($params["onClick"])!="")
				       		{
				       			$js .= $params["onClick"];
				       		}
				       		$urlElement->setParam("onClick",$js);
				       }

				       return  $urlElement->getUrl($text,$url,$target);
		}

	}


?>