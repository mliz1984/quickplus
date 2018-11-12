<?php
    require_once($_SERVER['DOCUMENT_ROOT']."/lib/datamsg.php");
    require_once($_SERVER['DOCUMENT_ROOT']."/lib/quickMenu.php");
     require_once($_SERVER['DOCUMENT_ROOT']."/lib/commonTools.php");
    if(!isset($_SESSION)){	
		session_start();
	}
	class LoginManager 
	{
		protected $sessionArea = "_quickplus_logininfo";	
        protected $userinfo = null;
        protected $rightinfo = null;
        protected $menuClassMapping = Array();
        protected $lastUrlSession = "adminmenu";
        public function logout()
        {
        	unset($_SESSION[$this->sessionArea]);
        	session_destroy();
        }
        public function getLastUrlSession()
        {
        	return $this->lastUrlSession;
        }
       
        public function login($db,$username,$password)
		{
			
			
			$result = false;
			$db = new DataBase();
			$data = new Data($db,"qp_rightcontrol","login");
			$data->set("login",$username);
			$data->set("password","password('".$password."')");
			$data->setKeepOri("password",true);
			$datamsg = $data->find();
			if($datamsg->getSize()>0)
			{
				     $udata = $datamsg->getData(0);
					 if($udata->getInt("can_login")==1)
					 {
					 	 $userinfo = Array();
					     $userinfo["accountid"] = strtolower($username);
					     $userinfo["id"] = strtolower($username);
					 	 $userinfo["can_login"] = $udata->getInt("can_login");
					 	 $userinfo["is_admin"] = $udata->getInt("is_admin");
					 	 $userinfo["access_right"] = $udata->getString("right");
					 	 $_SESSION[$this->sessionArea] = $userinfo;
					 	 $userinfo["groupid"]= $groupid;
						 $rightinfo = $this->loadRightInfo($db,$id,$groupid);
						 $userinfo["menu"]= $rightinfo["menu"];
						 $userinfo["rightinfo"] = $rightinfo["rightinfo"];
						 $userinfo["menuClassMapping"] = $this->menuClassMapping;
					     $_SESSION[$this->sessionArea] = $userinfo;
					   
						 $result = true;
					}
				
			}
			return $result;
		}

		public function reloadUserRight($db)
		{
			$result = false;
			$userinfo = ArrayTools::getValueFromArray($_SESSION,$this->sessionArea);
			$id = $userinfo["accountid"];
			if(is_array($userinfo)&&$userinfo["accountid"]!=null&&trim($userinfo["accountid"])!="")
			{
			    $result = true;
				$groupid = $userinfo["groupid"];
				$rightinfo = $this->loadRightInfo($db,$id,$groupid);
				$userinfo["menu"]= $rightinfo["menu"];
				$userinfo["rightinfo"] = $rightinfo["rightinfo"];
			    $userinfo["menuClassMapping"] = $this->menuClassMapping;
			    $_SESSION[$this->sessionArea] = $userinfo;
			}
			return $result;
		}

		public function checkLogin($getUserInfo=false)
		{
			
			$result = false;
			$userinfo = ArrayTools::getValueFromArray($_SESSION,$this->sessionArea);
				if(is_array($userinfo)&&$userinfo["accountid"]!=null&&trim($userinfo["accountid"])!="")
				{
					$result = true;
					if($getUserInfo)
					{
						$result =  $userinfo;
					}
				}
				
			return $result;
		}


		public function getMenu()
		{
			
			$result = false;
	        $userinfo = $this->checkLogin(true);

	        if(is_array($userinfo)&&isset($userinfo["menu"]))
	        {
	        	$result = $userinfo["menu"];
	        }

	        return $result;
		}

		public function getMenuClassMapping($id)
		{
	        $userinfo = $this->checkLogin(true);
	        if(is_array($userinfo)&&isset($userinfo["menuClassMapping"]))
	        {
	        
	        	$result = $userinfo["menuClassMapping"][$id];
	        }

	        return $result;
		}

		 protected function GetCurUrl($oriurl=null){
            if($oriurl==null)
            {
                $oriurl = $_SERVER['REQUEST_URI'];
            }
            $url='http://';
            if(isset($_SERVER['HTTPS'])&&$_SERVER['HTTPS']=='on'){
                $url='https://';
            }
            if($_SERVER['SERVER_PORT']!='80'){
                $url.=$_SERVER['SERVER_NAME'].':'.$_SERVER['SERVER_PORT'];
            }else{
                $url.=$_SERVER['SERVER_NAME'];
            }
            $url .=$oriurl;
            return $url;
        }

        public function goToErrorPage()
		{	
			$str =  StringTools::conv('Please login at first!',QuickFormConfig::$encode);
			$str = "<script>alert('".$str ."');top.location.href='/';</script>";
			echo $str;
			
		}

	   public function checkRight($url,$method=null,$src=null)
		{
			$result = false;

            if($this->checkLogin())
            {
			    $is_admin = intval($this->getUserInfo("is_admin"));
	 				
			    if($is_admin==1)
			    {
			    	$result = true;
			    }
			    else
			    {
			    	
			    	$rightInfo = $this->getUserInfo("rightinfo");
			    	if(is_array($rightInfo)&&is_bool($rightInfo[$url])&&$rightInfo[$url])
			    	{
			    		$result = true;
			    	}
			    	else if(!isset($rightInfo[$url]))
			    	{
			    		$result = true;
			    	}
			    	else if($src[QuickFormConfig::$menuIdMark]!=null&&trim($src[QuickFormConfig::$menuIdMark])!="")
			    	{
			    		$fullurl = $url."?".QuickFormConfig::$menuIdMark."=".$src[QuickFormConfig::$menuIdMark];
			    		if(is_array($rightInfo)&&is_bool($rightInfo[$fullurl])&&$rightInfo[$fullurl])
				    	{
				    		$result = true;
				    	}
			    	}
			    }
			}
		    return $result;
		}
		public function getGroupID()
		{
			//echo '@@2';
			return $this->getUserInfo("groupid");
		}
		public function getAccountID()
		{
			return $this->getUserInfo("accountid");
		}
		public function getAccountDbID()
		{
			return $this->getUserInfo("id");
		}
		public function getUserInfo($key=null)
		{
			$result = false;
			$userinfo = $this->checkLogin(true);
			
			if(is_array($userinfo))
			{
				if($key!=null&&trim($key)!="")
				{
					$result = $userinfo[$key];
				}
				else
				{
					$result = $userinfo;
				}
			}
		
			return $result;
		}
        protected function setSessionArea($sessionArea)
        {
        	$this->sessionArea = $sessionArea;
        } 
        protected function getSessionArea()
        {
        	return $this->sessionArea;
        }

       protected function getMenuInfo($db,$menuMapping,$menuData)
        {
        	$ids = "";
        	$tmp = Array();
        
        	foreach($menuData as $id => $right)
        	{	
        		$add = $right; 
        		if($add)
        		{
        			$ids .=",'".$id."'";
        			$pageInfo = $menuMapping[strval($id)];
        			$parentid = trim($pageInfo["parentpage_id"]);
        			$parentInfo = $menuMapping[strval($parentid)];
        			if(is_array($parentInfo)&&($parentInfo["parentpage_id"]==null||intval($parentInfo["parentpage_id"])==0))
        			{
        				if(!in_array(intval($parentid), $tmp))
        				{
        					$tmp[] = intval($parentid);
        					$ids .=",'".$parentid."'";
        				}

        			}
        		}	
        	}
        	$ids = ltrim($ids,",");
            $sql = "SELECT a.id,a.name,a.showname,a.order_sequence,a.parentpage_id,a.url,a.target,a.classsrc,a.classname,a.viewsrc FROM qp_menu_manage a WHERE a.id IN (".$ids.")  ORDER BY a.parentpage_id,a.order_sequence,a.name";
            $dataMsg = new DataMsg();
        	$dataMsg->findBySql($db,$sql);
        	$menu = new QuickMenu();
        	$menu->setLastUrlSession($this->lastUrlSession);
        	for($i=0;$i<$dataMsg->getSize();$i++)
        	{
        		$data = $dataMsg->getData($i);
                $id = $data->getInt("id");
        		$parentid = $data->getInt("parentpage_id");
        		$url = $data->getString('url');
				$target = $data->getString('target');
        		$showname = $data->getString('showname');
        		$classsrc = $data->getString("classsrc");
     			$classname = $data->getString("classname");
     			$viewsrc = $data->getString("viewsrc");
        		if($parentid==0)
        		{
        			$menu->addCategory($id,$parentid,$showname);
        		}
        		else
        		{
        			if($url==null||trim($url)=="")
        			{
        				if($classsrc!=null&&trim($classsrc)!=""&&$classname!=null&&trim($classname)!=""&&$viewsrc!=null&&trim($viewsrc)!="")
        				{
        					$url =$viewsrc."?".QuickFormConfig::$menuIdMark."=".$id;
        					$this->menuClassMapping[$id] = Array("classsrc"=>$classsrc,"classname"=>$classname,"viewsrc"=>$viewsec);

        				}
        		    }

        			$menu->addData($id,$parentid,$showname,$url,$target);
        		}
        	}	
    
        	return $menu;
        
        }
        protected function loadRightInfo($db,$id,$groupid)
        {

        	$data = new Data($db,"qp_menu_manage","id");
 			$data->set("is_active","1");
 			$data->addOrder("parentpage_id");
 			$datamsg= $data->find();
 			$result = Array();
 			$menuData = Array();
 			$menuinfo = $datamsg->getDataArray();
 			$menuMapping = $datamsg->getKeyDataArray("id",true,true);
 			$is_admin = $this->getUserInfo("is_admin");
           
 			foreach($menuinfo as $m)
 			{
 				$id = intval($m["id"]);
 		        $parentid = intval($m["parentpage_id"]);
 		        $hasRightControl = $m["has_right_control"];
 		        $url = $m["url"];
 		         if($url==null||$url=="")
 		        {
 		        	$viewsrc = $m["viewsrc"];
 		        	$classname = $m["classname"];
 		        	$classrc = $m["classsrc"];
 		        	if($viewsrc!=null&&trim($viewsrc)!=""&&$classname!=null&&trim($classname)!=""&&$classrc!=null&&trim($classrc)!="")
 		        	{
 		        		$url = $viewsrc."?".QuickFormConfig::$menuIdMark."=".$id;
 		        	}
 		        }
 		        if($hasRightControl!=null&&trim($hasRightControl)!=""&&intval($hasRightControl)==1&&!$is_admin) 
 		        {
 		        	if($parentid!=null&&$parentid!=0)
 		        	{
	 		        	$menuData[strval($id)] = false;
	 		        	$result[$url] = false;
	 		        }
	 		        else
	 		        {
	 		        	$menuData[strval($id)] = true;
 		        	    $result[$url] = true;
	 		        }
 		        }
 		        else
 		        {
 		        	$menuData[strval($id)] = true;
 		        	$result[$url] = true;
 		        } 
 			}
 			

 			if(!$is_admin)
 			{
 				

 				$right = $this->getUserInfo("access_right");
 			 
 				$rightArray = explode(",", $right);
 				foreach($rightArray as $r)
 				{

 					$url = trim( $menuMapping[strval($r)]["url"]);
 					
 					$menuData[strval($r)] = true;
 					$result[$url] = true;

 				}

 			}
 			$result["menu"] = $this->getMenuInfo($db,$menuMapping,$menuData);
 			
 			$result["rightinfo"] = $result;
 			return $result;

        }

	}