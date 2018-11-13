<?php
namespace Quickplus\Lib;
use Quickplus\Lib\quickForm as quickForm;
use Quickplus\Lib\QuickTemplateConfig as QuickTemplateConfig;

     class QuickTemplateTools extends QuickTemplateConfig
     {

        
         public function getSqlByApiKey($db,$apiKey)
         {
                $data = new Data($db,$this->getSqlTableName(),"id");
                $data->set("apikey",$apiKey);
                $dataMsg = $data->find();
                $result = null;
                if($dataMsg->getSize()>0)
                {
                    $data = $dataMsg->getData(0);
                    $sql =$data->getString("sql");
                    if($sql!=null&&trim($sql)!="")
                    {
                        $result = $sql;
                    }
                }
                return $result;
         }

         public function getTemplateArray($db,$apiKey,$category)
         {
              $data =new Data($db,$this->getDbTableName(),"id");
              $data->set("apikey",$apiKey);
              $data->set("category",$category);
              $data->set("ispublish","1");
              $data->addOrder("id");
              $dataMsg = $data->find();
              $result = Array();
              for($i=0;$i<$dataMsg->getSize();$i++)
              {
                 $data = $dataMsg->getData($i);
                 $language = $data->getString("language");
                 $title = $data->getString("title");
                 $content = $data->getString("content");
                 $template = Array("title"=>$title,"content"=>$content);
                 if($language==null||trim($language==""))
                 {
                    $result["default"] = $template;
                 }
                 else
                 {
                    $langArray = explode(",",$language);
                    foreach($langArray as $lang)
                    {
                        $result["language"][strval($lang)] = $template;
                    }
                 }
              }
              return $result;
         }

         public function getTempalteByLanguage($templateArray,$langCode,$defaultLangCode=null,$usedefault=true)
         {
           
            $result = $templateArray["language"][$langCode];
            if(!is_array($result))
            {
                if($defaultLangCode!=null&&trim($defaultLangCode)!="")
                {
                      $result = $templateArray["language"][$defaultLangCode];
                }
                if(!is_array($result)&&$useDefault)
                {
                    $result = $templateArray["default"];
                }
            }
            return $result;
         }

         public function getContentByTempalte($template,$dataArray)
         {
            $result = Array();
           
            if(is_array($template))
            {
                $result["title"] = $template["title"];
                $content = $template["content"];
                if(is_array($dataArray))
                {
                    foreach($dataArray as $col => $value)
                    {
                        $old = $this->getTemplateStrByDbname($col);
                        $content = str_replace($old,$value,$content);
                    }
                }
                $result["content"] = $content;
            }
            return $result;
         }



     }
     class QuickTemplateSql extends QuickTemplateConfig 
     {
            
            public function getSql($src=null)
            {
                $sql = "SELECT
                          a.`id`,
                          a.`name`,
                          a.`sql`,
                          a.`apikey`,
                          a.`isenabled`
                        FROM `".$this->getSqlTableName()."` a";
                return $sql;
            }
            public function preLoad($db,$src=null)
            {
                $statusArray = Array();
                $statusArray[] = Array("id"=>"1","name"=>"Yes");
                $statusArray[] = Array("id"=>"0","name"=>"No");
                $this->addAttachData("Status",$statusArray);
            }
            public function init($src=null)
            {

                $this->addField("id","ID");
                $this->addField("name","Name");
                $this->addField("sql","SQL");
                $this->addField("apikey","Api Key");
                $this->addField("isenabled","Enable?");
                $this->addField("operation","Operation");
                $this->setRaeFieldType("name",true,"editUrlReportMode");
                $this->setRaeFieldType("apikey");
                $this->setTranslateSetting("isenabled",false,true);
                $this->setRaeFieldType("isenabled",true,"translateByStatus");
                $this->setReportFieldType("operation",true,"getOperation");
                $this->setSearchFieldType("name","defaultSearchShowMode");
                $this->setSearchFieldType("apikey","defaultSearchShowMode");
                $this->setSearchFieldType("isenabled","getSelectByStatusWithAll");

            }

            public function initEdit($src=null)
            {
                $this->setIsAdd(true);
                $this->setIsEdit(true);
                $this->setIsDelete(true);
                $this->setMainIdCol("id");
                $this->setTable("id",$this->getSqlTableName(),"a");
                $this->setDeleteTable("id",$this->getSqlTableName(),"a");
                $this->setEditFieldType("name","defaultSearchShowMode");
                $this->setEditFieldType("apikey","defaultSearchShowMode");
                $this->setEditFieldType("isenabled","getSelectByStatus");
                $this->setEditFieldType("sql","textAreaSearchShowMode");
                $this->addValidateRule("apikey","quickAjax","checkApiKey","Dublicate Api Key, Please choose another one.");
            }
         
            public function checkApiKey($db,$dbname,$mainid,$src)
            {
                $apikey = $src[$this->getEditPrefix()."apikey"];
                //echo $operatecode;
                $sql = $this->getSql()." WHERE a.`apikey` ='".$apikey."'";
                if($mainid!=null&&trim($mainid)!="")
                {
                        $sql .=" AND  a.`id` <> '".$mainid."'";
                } 
                $dataMsg = new DataMsg();
                $dataMsg->findBySql($db,$sql);
                $result = true;
                if($dataMsg!=null&&$dataMsg->getSize()>0)
                {
                    
                    $result = false;
                }
                return $result;
            }

            public function getOperation($row,$dbname,$export=false)
            {
                $value = $this->getValueByDbName($row,"apikey",$export);
                $html ="<a  href='".QuickFormConfig::$quickFormBasePath."quickTemplate.php?type=1&apikey=".$value."' target='_blank'>Template Manage</a>";
                return $html;
            }
  

        
     }
 	 class QuickTemplate extends QuickTemplateConfig
 	 {
 	 	 

		protected $classname = null;
		protected $sendRight = true;
    protected $type = 0;
    protected $languageSupport = false;
    protected $operateBy = null;
    protected $allowFixValue = true;
    protected $allowAmountLimit = true;
    protected $methodList = null;
    public function setMethodList($methodList)
    {
      $this->methodList = $methodList;
    }
    public function setAllowAmountLimit($allowAmountLimit)
    {
        $this->allowAmountLimit = $allowAmountLimit;
    }
    public function setOperateBy($operateBy)
    {
      $this->operateBy = $operateBy;
    }
    public function setAllowFixValue($allowFixValue)
    {
      $this->allowFixValue = $allowFixValue;
    }
		public function getSql($src=null)
        {
        	 $sql = "SELECT
						  a.`id`,
						  a.`name`,
						  a.`desciption`,
						  a.`content`,
						  a.`title`,
						  a.`classname`,
						  a.`classsrc`,
						  a.`range`,
						  a.`col`,
						  a.`fixvalue`,
						  a.`qtmethod`,
						  a.`qtmethodtype`,
                          a.`ispublish`,
                          a.`amountlimit`,
                          a.`type`,
                          a.`language`,
                          a.`apikey`,
                          a.`category`
						FROM `".$this->getDbTableName()."` a";
			 return $sql;
        } 

        public function preLoad($db,$src=null)
        {
                $statusArray = Array();
                $statusArray[] = Array("id"=>"1","name"=>"Yes");
                $statusArray[] = Array("id"=>"0","name"=>"No");
                $this->addAttachData("PublishStatus",$statusArray);
                $methodObj = $this->getMethodObj();
                if($src["type"]==1)
                {

                    $this->type = 1;
                    $languageList = $methodObj->getLanguageList($db);
                    if(is_array($languageList)&&count($languageList)>0)
                    {
                         $this->languageSupport = true;
                         $this->addAttachDataByMap("Language",$languageList);
                    }
                }
        }
	  protected function getClassName($src)
    {
        return $src["classname"];
    }   
    protected function initConfig($src=null)
    {

    }
    public function init($src=null)
		{

			$this->setIsTemplate(false);
      $this->addHidden("type",$this->type);
      $tClassName = $this->getClassName($src);
			if($this->type==0&&$tClassName!=null&&trim($tClassName)!="")
			{
				$classname = $tClassName;
				$whereClause = " a.`classname` = '".$classname."' AND a.`type` = '".$this->type."'";
				$tmp = FileTools::getClassFilePath($classname);
				$this->setIsAdd(true);
				if($tmp)
				{
					$classsrc = $tmp;
					$this->addTransfer("classsrc",$classsrc);
					$whereClause .= " AND a.`classsrc` = '".$classsrc."' ";
				}
				$this->addTransfer("classname",$classname);
				$this->addHidden("classname",$classname);
				$this->setWhereClause($whereClause);

			} 
            else if($this->type==1&&$src["apikey"]!=null&&trim($src["apikey"])!="")
            {
                $apikey = $src["apikey"];
                $whereClause  = " a.`apikey` = '".$apikey."' AND a.`type` = '".$this->type."'";
                $this->setIsAdd(true);
                $this->addTransfer("apikey",$apikey);
                $this->addTransfer("type",$this->type);
                $this->setWhereClause($whereClause);
            }
			else
			{
                $whereClause = " a.`type` = '".$this->type."'";
                $this->setWhereClause($whereClause);
				$this->setIsAdd(false);
			}
      			$this->addField("name","Name");
      			$this->addField("desciption","Desciption");
      			$this->addField("title","Title");
      			$this->addField("content","Content");
      			$this->addField("range","Range");
      			$this->addField("col","Col");
      			$this->addField("fixvalue","Fix Value");
      			$this->addField("qtmethod","Method");
      			$this->addField("qtmethodtype","Method Type");
            $this->addField("ispublish","Publish");
            $this->addField("amountlimit","Amount Limit");
            $this->addField("type","Type");
		        $this->setRaeFieldType("name",true,"editUrlReportMode");
            $this->setRaeFieldType("title",true,"editUrlReportMode");
            if($this->type==1)
            {
                $this->addField("apikey","Api Key");
                $this->addField("category","Category");
                $this->setRaeFieldType("apikey");
                $this->setRaeFieldType("category");
            }
            $this->setTranslateSetting("ispublish",false,true);
            $this->setTranslateSetting("language",true);
            if($this->languageSupport)
            {
                   $this->addField("language","Language");
                   $this->setRaeFieldType("language",true,"translateByLanguage");
            }
		        $this->setRaeFieldType("desciption");
            $this->setRaeFieldType("ispublish",true,"translateByPublishStatus");
		        $this->setSearchFieldType("name","defaultSearchShowMode");
            if($this->type==1)
            {
                 $this->setSearchFieldType("apikey","defaultSearchShowMode");
            }
            $this->setSearchFieldType("ispublish","getSelectByPublishStatusWithAll");
            $this->initConfig($src);
		}

		public function getMethodObj()
		{
		
			$obj  = null;
			try{
				
				if($this->methodClass!=null&&trim($this->methodClass)!=""&&$this->methodClassSrc!=null&&trim($this->methodClassSrc)!="")
	        	{		
	        			 include_once($_SERVER['DOCUMENT_ROOT'].$this->methodClassSrc);
	        			 $classname = $this->methodClass;
	        			 $obj  = new $classname();
	        		 
	        	}
	        }
        	catch (Exception $e) {

        		}
        	return $obj;
		}

		public function modifySaveDataArray($src,$dataArray,$forceAdd=false,$editPrefix=null)
        { 
             
             if($this->type==0)
             {
            	 $rangeArray = CommonTools::getDataArray($src,$this->getSearchPrefix());
                 $range = json_encode($rangeArray);
            	 $dataArray["range"] = $range;
            	 $dataArray["col"] = $src["qt_col"];
                 $dataArray["amountlimit"] = strval(intval($src["qt_amountlimit"]));
            	 $dataArray["fixvalue"] = $src["qt_fixvalue"];
            	 $qtmethod = $src["qt_qtmethod"];
    			 $dataArray["qtmethod"] = $qtmethod;
    			 $methodObj = $this->getMethodObj();
    			 $dataArray["qtmethodtype"] = $methodObj->getMethodType($qtmethod);	
             }
             return $dataArray;
        }

		public function initEdit($src=null)
		{
			$this->setIsEdit(true);
			$this->setIsDelete(true);
			$this->setMainIdCol("id");
	  		$this->setTable("id",$this->getDbTableName(),"a");
	  		$this->setDeleteTable("id",$this->getDbTableName(),"a");
			$this->setEditFieldType("name","defaultSearchShowMode");
			 if($this->type==1)
            {
                $this->setEditFieldType("category","defaultSearchShowMode");
                $this->addValidateRule("category","required",true);
            }
            $this->setEditFieldType("desciption","defaultSearchShowMode");
            if($this->languageSupport)
            {
                   $this->setEditFieldType("language","getCheckboxesByLanguage");
            }

            $this->setEditFieldType("ispublish","getSelectByPublishStatus");
			$this->setEditFieldType("title","defaultSearchShowMode");
			$this->setEditFieldType("content","editorSearchShowMode");
            $this->addEditHidden("type",$this->type);

           
		}

		public function initCustomMethod($src)
		{

			if($src["qt_send"]!=null&&trim($src["qt_send"])!=null&&intval($src["qt_send"])==1)
			{
			  $this->addMethod("add","execTemplateMethod",true);
			  $this->addMethod("update","execTemplateMethod",true);
			}
		}

 	 	public function execTemplateMethod($db,$src=null,$editPrefix=null,$where=null,$debug=null)
 	 	{

 	 		ignore_user_abort(true);

 	 		if($editPrefix==null&&trim($editPrefix)=="")
 	 		{
 	 			$editPrefix = $this->getEditPrefix();
 	 		}

 	 		$classname = $src["classname"];
 	 		$classsrc = $src["classsrc"];
 	 		$template = $src[$editPrefix."content"];
 	 		$title = $src[$editPrefix."title"];
 	 		$col = $src["qt_col"];
 	 		$fixvalue = $src["qt_fixvalue"];
 	 		$qtmethod = $src["qt_qtmethod"];
            $qtamountlimit = intval($src["qt_amountlimit"]);
            $templateid =  intval($src["qt_templateid"]);
            if($qtamountlimit<=0)
            {
                $qtamountlimit = 0;
            }
            $processid = null;
            $data = new Data($db,"qp_templateProcess","id");
            $data->set("methodname",$qtmethod);
            $data->set("processstatus",0);
            $data->set("templateid",$templateid);   
            $dataMsg = $data->find();
            $methodObj = $this->getMethodObj();
            if($methodObj->isDebug())
            {
                $qtmethod ="debugQuickTemplate";
            }
            if($dataMsg->getSize()==0)
            {
            	$create = new Data($db,"qp_templateProcess","id");
	            $create->set("methodname",$qtmethod);
	            $create->set("processstatus",0);
	            $create->set("templateid",$templateid);
	            $create->set("starttime", date("Y-m-d H:i:s"));
              $create->create();
	       
            }
            else
            {
            	return false;
            }
 	 		$src["searchSign"] ="1";
 	 		$result = false;
 	 		try
 	 		{
 	 			
 	 			$obj = new $classname();
 	 			if(is_bool($debug))
                {
                    $methodObj->setDebug($debug);
                }
 	 			if($methodObj->isDebug())
        		{
        		   print_r($src);
        		   $obj->setDebug(true);
        		}
 	 			$quickFormDrawer = new quickFormDrawer();
                if($where!=null&&$where!="")
                {
                     $quickFormDrawer->setWhere($where);
                }
                $quickFormDrawer->setBlank(false);
        	    $quickFormDrawer->setQuickForm($db,$obj);

        	    $obj = $quickFormDrawer->getForm($db,$src,1,$qtamountlimit,false,false);
        	    $result = $obj->getResult();
        	    $fields = $obj->getFields();
        	    $exportFields = $obj->getExportField();
        	    $reportFields = $obj->getReportField();
        	    $finished = Array();

        	    for($i=0;$i<count($result);$i++)
        	    {
        	    	$content = $template;
        	    	foreach($fields as $dbname =>$fieldInfo)
        			{ 
        				$method = null;

        				if($exportFields[$dbname]!=null&&trim($exportFields[$dbname])!="")
        				{
        					$method = $exportFields[$dbname];
        				}
        				elseif($reportFields[$dbname]!=null&&trim($reportFields[$dbname])!="")
        				{
        					$method = $reportFields[$dbname];
        				}
        				
        				$old = $this->getTemplateStrByDbname($dbname);
        				$new = $obj->getValueByMethod($i,$dbname,true,$method);
        				$content = str_replace($old,$new,$content);
         			}
             

         			$colvalue = null;
         			$dataArray = $result[$i];
        			if($col!=null&&trim($col)!=""&&trim($col)!="quicktemplate_use_fix_value")
        			{
        				$colvalue = $dataArray[$col];
        			}
        			elseif(trim($col)=="quicktemplate_use_fix_value"&&$fixvalue!=null&&trim($fixvalue)!="")
        			{
        				$colvalue = $fixvalue;
        			}
        			if($methodObj!=null&&$colvalue!=null&&trim($colvalue)!=""&&$qtmethod!=null&&trim($qtmethod)!="")
        			{
                        $sign = $colvalue.$title.$content;
                        if($finished[$sign]!=$sign)
                        {
                            
                            $methodObj->$qtmethod($db,$colvalue,$title,$content,$dataArray);
                            $finished[$sign] = $sign;
                        }

        				

        			}
        	    }
        	   	if($methodObj->isDebug())
        		{
        					eof();
        		}
        	    $result = true;

 	 		}
 	 		catch(Exception $e)
 	 		{
 	 			$result = false;
 	 		}
 	 		$update = new Data($db,"qp_templateProcess","id");
	        $update->setWhereOnly("methodname",$qtmethod);
	        $update->setWhereOnly("templateid",$templateid);
	        $update->setDataOnly("endtime", date("Y-m-d H:i:s"));
	        $update->setDataOnly("processstatus", 1);
	        $update->update();
 	 		return $result;

 	 	}

        public function processData($db,$src,$dataArray,$edit=false)
        {
            if($this->type==1)
            {
                $dataArray = $this->processDataForApi($db,$src,$dataArray,$edit);
            }
            else 
            {
                $dataArray = $this->processDataForNonApi($db,$src,$dataArray,$edit);
            }
            return $dataArray;
        }

        protected function processDataForApi($db,$src,$dataArray,$edit=false)
        {
            $apikey = "";
            if($edit)
            {
                if($dataArray["apikey"]!=null&&trim($dataArray["apikey"])!="")
                {
                    $apikey = $dataArray["apikey"];
                }
            }
            else 
            {
                if($src["apikey"]!=null&&trim($src["apikey"])!="")
                {
                    $apikey = $src["apikey"];
                }
            }
            $this->addEditHidden("apikey",$apikey);
            $this->addEditHidden("classname","api");
            $this->addEditHidden("classsrc","api");
            $this->addEditHidden("range","api");
            $this->addHidden("apikey",$apikey);
            if($apikey!=null&&trim($apikey)!="")
            {
               $quickTemplateTools = new QuickTemplateTools();
               $sql = $quickTemplateTools->getSqlByApiKey($db,$apikey);
               $fieldArray = DbTools::getColNames($sql);
               foreach($fieldArray as $k => $v)
               {
                    $this->setEditInputButton("content",$k,$this->getTemplateStrByDbname($k));
               }
            }
            return $dataArray;
        }
 	
 	 	protected function processDataForNonApi($db,$src,$dataArray,$edit=false)
        {
        	$classname = "";
        	$classsrc = "";
        	$fixvalue = "";    
        	$qtmethod = "";
            $amountlimit ="";
        	$col = null;
        	if($edit)
        	{
        		if($dataArray["classname"]!=null&&trim($dataArray["classname"])!="")
        		{
        			$classname = $dataArray["classname"];
        		}
        		if($dataArray["classsrc"]!=null&&trim($dataArray["classsrc"])!="")
        		{
        			$classsrc = $dataArray["classsrc"];
        		}
        		if($dataArray["fixvalue"]!=null&&trim($dataArray["fixvalue"])!="")
        		{
        			$fixvalue = $dataArray["fixvalue"];
        		}
        		if($dataArray["col"]!=null&&trim($dataArray["col"])!="")
        		{
        			$col = $dataArray["col"];
        		}
        		if($dataArray["qtmethod"]!=null&&trim($dataArray["qtmethod"])!="")
        		{
        			$qtmethod = $dataArray["qtmethod"];
        		}
                if($dataArray["amountlimit"]!=null&&trim($dataArray["amountlimit"])!="")
                {
                    $amountlimit = $dataArray["amountlimit"];
                }

        	}
        	else
        	{
        		if($src["classname"]!=null&&trim($src["classname"])!="")
        		{
        			$classname = $src["classname"];
        		}
        		if($src["classsrc"]!=null&&trim($src["classsrc"])!="")
        		{
        			$classsrc = $src["classsrc"];
        		}
        	}
        	$this->addEditHidden("classname",$classname);
        	$this->addHidden("classname",$classname);
        	$this->addEditHidden("classsrc",$classsrc);

        	$classObj = new $classname();
        	$quickFormDrawer = new quickFormDrawer();
        	$quickFormDrawer->setQuickForm($db,$classObj);
        	$fields = $classObj->getFields();
        	$i = 0; 
          $dbNameArray = $this->operateBy;
          if(!is_array($dbNameArray))
          {
            	$dbNameArray = Array();
            	foreach($fields as $dbname =>$fieldInfo)
            	{
                    if($dbname != $classObj->getCustomColMark())
                    {
                		$i++;
                		$echo = $fieldInfo["displayname"];
                		$this->setEditInputButton("content",$echo,$this->getTemplateStrByDbname($dbname));
                		$dbNameArray[$dbname] = trim($fieldInfo["displayname"]);
                    }
            	}
                $customTemplateButton = $classObj->getCustomTemplateButtonArray();

                foreach($customTemplateButton as $key =>$buttonInfo)
                {
                    $name = $buttonInfo["name"];
                    $value = $buttonInfo["value"];
                    $this->setEditInputButton("content",$name,$value);
                }
          }
          if($this->allowFixValue)
          {
        	   $dbNameArray["quicktemplate_use_fix_value"] = "Use Fix Value";
          }
        	$range = $dataArray["range"];
        	$tmp = Array();
        	if($range!=null&&trim($range!=""))
        	{

        		$tmp = json_decode($range);
        	}
        	
        	$rangeArray = Array();
        	foreach($tmp as $key => $value)
        	{
        		$rangeArray[$this->getSearchPrefix().$key] = $value;
        	}
            $searchHtmlStr = $quickFormDrawer->getSearchBarHtml($rangeArray,true,false);
          	if($searchHtmlStr!=null&&trim( $searchHtmlStr)!="")
            {
  	        	$rangeHtml = "<table>". $searchHtmlStr."</table>";
  	        	$this->addEditCustomHtml("range","Range",$rangeHtml);
            }
            $methodList = $this->methodList;
	        	$methodObj = $this->getMethodObj();
        		if((is_array($methodList)&&count($methodList)>0)||($methodObj!=null&&$methodObj->getMethodSize()>0))
        		{
              $hiddenHtml = "";
              if(!is_array($methodList))
              {
                  $methodList = $methodObj->getMethodList();
              }
        			$methodChooser = new HtmlElement("qt_qtmethod","qt_qtmethod");
        			$methodChooser->setParam("class","form-control");
        			$this->addEditCustomHtml("Method","Method",$methodChooser->getSelect($methodList,$qtmethod,true));
               if(count($dbNameArray)>1)
               {
    		        	$colChooser = new HtmlElement("qt_col","qt_col");
    		        	$colChooser->setParam("class","form-control");
    		        	$js .= '<script>$("#qt_col").change(function(){
    								  if($("#qt_col").val()!="quicktemplate_use_fix_value"){$("#qt_fixvalue").val("");$("#qt_fixvalue").attr("disabled","disabled");}
    								  else
    								  	{$("#qt_fixvalue").removeAttr("disabled");}
    								});</script>';
                  
    		        	$this->addEditCustomHtml("Operate By","Operate By",$colChooser->getSelect($dbNameArray,$col,true).$js);
                }
                else
                {
                   $colHidden = new HtmlElement("qt_col","qt_col");
                   $hiddenValue = "";
                   foreach($dbNameArray as $k => $v)
                   {
                      $hiddenValue = $k;
                   }
                  $hiddenHtml = $colHidden->getHidden($hiddenValue);
                }
                if($this->allowFixValue)
                {
  		            $customFieldHtml = new HtmlElement("qt_fixvalue","qt_fixvalue");
  		            $customFieldHtml->setParam("class","form-control");
  		            $js2 .= '<script>if($("#qt_col").val()!="quicktemplate_use_fix_value"){$("#qt_fixvalue").attr("disabled","disabled");}</script>';
  		            $this->addEditCustomHtml("Fix Value","Fix Value",$customFieldHtml->getInput($fixvalue).$js2);
                }
		            $sendHtml = new HtmlElement("qt_send","qt_send");
		            $js3 .="if($('#qt_send').val()==1 && $('#qt_col').val()=='quicktemplate_use_fix_value' && $('#qt_fixvalue').val() ==''){alert('please input fix value.');$('#qt_send').val(0);this.checked =false} ";
                    $limitFieldHtml = new HtmlElement("qt_amountlimit","qt_amountlimit");
                    $limitFieldHtml->setParam("class","form-control");
                    $hidden = new HtmlElement("qt_templateid","qt_templateid");
                   if($this->sendRight)
                   {
                     if($this->allowAmountLimit)
                     {
                      	$this->addEditCustomHtml("Amount Limit","Amount Limit",$limitFieldHtml->getInput($amountlimit));
                      }
		            	$this->addEditCustomHtml("Send","Send", $hiddenHtml.$sendHtml->getCheckBoxWithHidden(null,1,0,$js3).$hidden->getHidden($src["ed_dataid"]));

		           }
	      	    }

            return $dataArray;
        }
 	 }

 	 class QuickTemplateDesigner extends QuickTemplate
 	 {
 	 		protected $sendRight = false;
 	 }

     require_once(dirname(__FILE__)."/quickform/include.php");

?>