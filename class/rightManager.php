<?php
set_time_limit(0);
require_once($_SERVER['DOCUMENT_ROOT']."/vendor/autoload.php");
use Quickplus\Lib\DataMsg\Data;
use Quickplus\Lib\quickForm;
use Quickplus\Lib\Tools\CommonTools;
class RightManager extends quickForm
{
	protected $sql = "SELECT 
                  a.`login`,
                  a.`can_login`,
                  a.`is_admin`,
                  a.`right` 
                FROM
                  `qp_rightcontrol`  a";
    protected $pageInfo = Array();
	public function preLoad($db,$src=null)
    {
        $status = Array("1"=>"Yes","0"=>"No");
        $this->addAttachDataByMap("Status",$status);
        $data = new Data($db,"qp_menu_manage","id");
        $dataMsg = $data->find();

        $this->addTreeData("PageTreeData",$dataMsg->getDataArray(),"id","name","parentpage_id",0);
        $this->pageInfo = $dataMsg->getKeyDataArray("id",true,true);

    }
	public function init($src=null)
	{
	
		 $this->addField("login","Username");
         $this->addField("password","Password");
		 $this->addField("can_login","Login Right");
		 $this->addField("is_admin","Admin Right");
		 $this->addField("right","Access Right");
  		 $this->setRaeFieldType("login",true,"editUrlReportMode");
		 $this->setRaeFieldType("can_login",true,"translateByStatus");
         $this->setRaeFieldType("is_admin",true,"translateByStatus");
         $this->setRaeFieldType("right",true,"getAccessRight");
		 $this->setSearchFieldType("login","multiLineLikeSearchShowMode");

	}
       
	

	public function initEdit($src=null)
	{
		    $this->setMainIdCol("login");
			$this->setIsAdd(true);
            $this->setIsEdit(true);
			$this->setIsChoose(true);
			$this->setTable("login","qp_rightcontrol","a");
			$this->setDeleteTable("login","qp_rightcontrol","a");
            if(!$this->isAddMode())
            {
                $this->setEditFieldType("login","readOnlySearchShowMode");
            }
            else
            {
			     $this->setEditFieldType("login","defaultSearchShowMode");
            }
			$this->setEditFieldType("password","defaultSearchShowMode");
			$this->setEditDefaultValue("is_admin","0");
		    $this->setEditDefaultValue("can_login","0");
			$this->setEditFieldType("can_login","getSelectByStatus");
			$this->setEditFieldType("is_admin","getSelectByStatus");
			$this->setEditFieldType("right","getTreeByPageTreeData");

	}

     
    public function getAccessRight($rows,$dbname,$export)
    {
        $ret ="None";
        $userid = strval($this->getValueByDbname($rows,"id",false));
        if(intval($this->getValueByDbname($rows,"can_login",false))==1)
        {
            if(intval($this->getValueByDbname($rows,"is_admin",false))==1)
            {
                $ret = "All";
            }
            else
            {
                $right = trim(strval($this->getValueByDbname($rows,"right",false)));
                $rightArray = explode(",", $right);
                $hh = "<br>";
                if($export)
                {
                    $hh="\n";
                }
                if(count($rightArray)>0)
                {
                      $ret = "";
                }
                foreach($rightArray as $r)
                {
                    $showname = $this->pageInfo[$r]["showname"];
                    $parentid = strval($this->pageInfo[$r]["parentpage_id"]);
                    $menuname = $this->pageInfo[$parentid]["showname"];
                    $fullname = $menuname.">".$showname;
                    $ret.= $hh.$fullname;
                }
                $ret = ltrim($ret,$hh);
            }
        }
        return $ret;
    }

   
    
    public function saveFormData($db,$src,$forceAdd=false,$editPrefix=null)
    {

    	if($editPrefix==null)
        {
                $editPrefix = $this->getEditPrefix(); 
        }
        
        $src =  $this->prepareCheckboxsDataForEdit($src);
        $dataArray = CommonTools::getDataArray($src, $editPrefix); 
        $access_right = "";
        if(isset($dataArray['right'])&&$dataArray['right']!=null&&trim($dataArray['right'])!="")
        {
	        $data = new Data($db,"qp_menu_manage","id");
	        $where = "parentpage_id IS NOT NULL AND parentpage_id <> '0' AND (id IN (".$dataArray['right'].") OR parentpage_id IN (".$dataArray['right']."))";
	        $data->setWhereClause($where);
	        $datamsg =  $data->find();
			$access_right =$datamsg->getIdstrs("id",",","");

	    }
        $data = new Data($this->getDb(),"qp_rightcontrol","id");
        
        $data->set("login",$dataArray["login"]);
        
        if(isset($dataArray['password'])&&$dataArray["password"]!=null&&$dataArray["login"]!="")
        {
            $data->set("password","password('".$dataArray["password"]."')");
        }
        $data->setKeepOri("password",true);
        $data->set("can_login",$dataArray["can_login"]);
        $data->set("is_admin",$dataArray["is_admin"]);
        $data->set("right",$access_right);
       $result = $data->createUpdate();
       $array = array("result"=>$result,"src"=>$src);
       return $array;
       
    }

}