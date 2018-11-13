<?php
	require_once($_SERVER['DOCUMENT_ROOT']."/lib/quickForm.php");
	class MenuManager extends quickForm
	{
	    protected $sql = "SELECT a.id,a.parentpage_id,a.url,a.order_sequence,a.name,a.showname,a.target,a.has_right_control,a.is_active,a.classsrc,a.classname,a.viewsrc FROM qp_menu_manage a";

	    public function preLoad($db,$src=null)
        {
        	
            $status = Array("1"=>"Yes","0"=>"No");
            $this->addAttachDataByMap("Status",$status);
        }
        public function getReportName()
        {
        	return "Menu Manage";
        }
        public function getWhereClause()
        {
        	return " (a.parentpage_id is null or a.parentpage_id='' or a.parentpage_id = 0) ";
        }
        
        public function initField($src=null)
        {
                //$this->setDebug(true);
        	$this->setRaeFieldType("showname");
        	$this->setRaeFieldType("name");
        	$this->setQuickEditFieldType("order_sequence","defaultSearchShowMode");
        	$this->setQuickEditFieldType("is_active","getSelectByStatus");
        	$this->setReportFieldType("operate",true,"getOperate");
        	$this->setSearchFieldType("showname","defaultSearchShowMode");
        	$this->setSearchFieldType("is_active","getSelectByStatusWithAll");

        }

        public function getOperate($row,$dbname,$export=false)
        {
        	$id = $this->getValueByDbname($row,"id",false);
        	$url = "pageManager.php?categoryid=".$id;
        	$html = new HtmlElement();
        	return $html->getUrl("Manage",$url);

        }

		public function init($src=null)
		{
			$this->setSearchBar(true);
			$this->setOrderFieldType("order_sequence","ASC");
			$this->setOrderFieldType("showname","ASC");
			$this->addField("id", "ID");
			$this->addField("parentpage_id", "Parent ID");
            $this->addField("classsrc", "Class Src");
            $this->addField("classname", "Class Name");
            $this->addField("viewsrc","View");
			$this->addField("url", "Url");
			$this->addField("order_sequence", "Order Sequence");
			$this->addField("name", "Name");
			$this->addField("showname", "Title");
			$this->addField("target", "Target");
			$this->addField("has_right_control","Right Control");
			$this->addField("is_active","Is Active");
			$this->addField("operate","Operate");
			$this->initField($src);
			
			
		}
		public function initEdit($src=null)
		{
			$this->setMainIdCol("id");
			$this->setIsAdd(true);
			$this->setIsDelete(true); 
			$this->setIsEdit(true);
			$this->setTable("id","qp_menu_manage","a");
			$this->setDeleteTable("id","qp_menu_manage","a");
			$this->setEditFieldType("showname","defaultSearchShowMode");
			$this->setEditFieldType("name","defaultSearchShowMode");
			$this->setEditFieldType("order_sequence","defaultSearchShowMode");
        	$this->setEditFieldType("is_active","getSelectByStatus");
		}
	}



        class PageManager extends MenuManager
        {
                public function preLoad($db,$src=null)
            {
                parent::preLoad($db,$src);
                $data = new Data($db,"qp_menu_manage","id");
                $data->setWhereClause("(parentpage_id is null or parentpage_id='' or parentpage_id = 0)");
                $this->addAttachDataByData($db,"Parent",$data,"id","showname");
                $this->addAttachDataByMap("View",QuickFormConfig::$viewList);
            }

        public function getWhereClause()
        {
                return " (a.parentpage_id is not null and a.parentpage_id <> '' and a.parentpage_id  <>  0) ";
        }

        public function getReportName()
        {
                return "Page Manage";
        }

        public function initField($src=null)
        {
                $this->setSearchMapping("parentpage_id","categoryid");
                $this->setRaeFieldType("showname");
                $this->setRaeFieldType("name");
                $this->setQuickEditFieldType("parentpage_id","getSelectByParent");
                $this->setQuickEditFieldType("has_right_control","getSelectByStatus");
               $this->setQuickEditFieldType("order_sequence","defaultSearchShowMode");
                $this->setQuickEditFieldType("is_active","getSelectByStatus");
                $this->setSearchFieldType("showname","defaultSearchShowMode");
                $this->setSearchFieldType("parentpage_id","getSelectByParentWithAll");
                $this->setSearchFieldType("has_right_control","getSelectByStatusWithAll");
                $this->setSearchFieldType("is_active","getSelectByStatusWithAll");
                if(isset($src[$this->getSearchPrefix()."parentpage_id"])&&$src[$this->getSearchPrefix()."parentpage_id"]!=null&&trim($src[$this->getSearchPrefix()."parentpage_id"])!="")
                        {
                                $this->addTransfer("categoryid",trim($src[$this->getSearchPrefix()."parentpage_id"]));
                        }
                        else if(isset($src["categoryid"])&&$src["categoryid"]!=null&&trim($src["categoryid"])!="")
                        {
                                $this->addTransfer("categoryid",trim($src["categoryid"]));
                        }
        }

        
        public function initEdit($src=null)
                {
                        $this->setMainIdCol("id");
                        $this->setIsAdd(true);
                        $this->setIsDelete(true); 
                        $this->setIsEdit(true);
                        $this->setTable("id","qp_menu_manage","a");
                        $this->setDeleteTable("id","qp_menu_manage","a");
                        $this->setEditFieldType("showname","defaultSearchShowMode");
                        $this->setEditFieldType("name","defaultSearchShowMode");
                        $this->setEditFieldType("url","defaultSearchShowMode");
                        $this->setEditFieldType("classsrc","defaultSearchShowMode");
                        $this->setEditFieldType("classname","defaultSearchShowMode");
                        $this->setEditFieldType("viewsrc","getSelectByView");
                        $this->setEditFieldType("target","defaultSearchShowMode");
                        $this->setEditFieldType("parentpage_id","getSelectByParent");
                        $this->setEditFieldType("has_right_control","getSelectByStatus");
                        $this->setEditFieldType("order_sequence","defaultSearchShowMode");
                $this->setEditFieldType("is_active","getSelectByStatus");
                $this->setEditDefaultValue("target","right");
                if(isset($src["categoryid"])&&$src["categoryid"]!=null&&trim($src["categoryid"])!="")
                {
                        $this->setEditDefaultValue("parentpage_id",trim($src["categoryid"]));
                }
                }

        }
?>