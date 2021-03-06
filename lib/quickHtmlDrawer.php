<?php
namespace Quickplus\Lib;;
use Quickplus\Lib\Tools\HtmlElement;
class quickHtmlDrawer extends quickFormDrawer 
 	{
 		protected $withTitle = true;
 	    protected $withPanel = true;
 		protected $search = false;
 		protected $parameter = Array();
 		protected $formId = null;
 		protected $ajaxTimeout = 0;
 		protected $panelName = null;
 		public function setAjaxTimeout($ajaxTimeout)
 		{
 			 $this->ajaxTimeout = $ajaxTimeout;
 		}
 		public function setWithPanel($withPanel)
 		{
 			$this->withPanel = $withPanel;
 		}
 		public function setPanelName($panelName)
 		{
 			$this->panelName = $panelName;
 		}
 		public function setSearch($search)
 		{
 			$this->search = $search;
 		}
 		public function setParameter($key,$value)
 		{
 			$this->parameter[$key] = $value;
 		} 
 		public function quickHtmlDrawer($formId)
 		{
 			$this->setFormId($formId);
 		}
 		public function setFormId($formId)
 		{
 			$this->formId = $formId;
 		}

 		public function getFormId()
 		{
 			return $this->formId;
 		}

 		public function setWithTitle($withTitle)
 		{
 			$this->withTitle = $withTitle;
 		}

 		public function getWithTitle()
 		{
 			return  $withTitle;
 		}

 		
 		public function getTableId()
 		{
 			return  "quickTable_".$this->formId;
 		}
 		
 		public static function getEditDataFormHtml()
 		{

 			$result = ' <form name="editData" id="editData"  action="/include/quickform/editData.php" target="_editData" method="post">';
 			$result .=' <input type="hidden" id="ed_isreport" name="ed_isreport" value=""/> ';
 			$result .=' <input type="hidden" id="ed_formmark" name="ed_formmark" value=""/> ';
 			$result .=' <input type="hidden" id="ed_dataid" name="ed_dataid" /></form>';
 			return $result;
 		}

 		public function getDataTableJson($draw,$obj,$colArray=null,$dataPart=false)
 		{	
 			$tableData = Array();
 			$titleinfo = $obj->getTitleInfo();
 			$resultSize = $obj->getResultSize(); 
 			$totalCount = $obj->getTotalCount();
			
 			 $cols = $titleinfo;
 			 if(is_array($colArray)&&count($colArray)>0)
 			 {
 			 	$cols =$colArray;
 			 }
 			if($dataPart===true)
 			{
 				$totalCount = $resultSize;
 			}
  			for($j=0;$j<$resultSize;$j++)
		    {
		    	$tmp = Array();
		    	 foreach($cols as $dbname =>$title)
		         {
		         		$tmp[] =  $obj->getValueByDbName($j,$dbname);

		         }
		         $tableData[] = $tmp;
		    }
		    return json_encode(array(
				    "draw" => intval($draw),
				    "recordsTotal" => $totalCount,
				    "recordsFiltered" => $totalCount,
				    "data" => $tableData
				));
 		}

 		public function getParameterString()
 		{
 			$result = "";
 			foreach($this->parameter as $key=>$value)
 			{
 				$result .="&".$key."=".$value;
 			}
 			return $result;
 		}

 		public function getIngridJs($obj)
 		{   
 			
 			$titleinfo = $obj->getTitleInfo();
 			$width = "";
 			foreach($titleinfo as $dbname =>$title)
          	{
          		$width .="225,";
          	}
          	$width = trim($width,",");
          	$isreport = "&isreport=0";
          	if($obj->isReport())
          	{
          		$isreport = "&isreport=1";	
          	}
 			$result .='<script type="text/javascript" src="'.QuickFormConfig::$jquery.'"></script>';
 			$result .='<script type="text/javascript" src="'.QuickFormConfig::$ingridPath.'js/jquery.ingrid.js"></script>';
 		    $result .='<link href="'.QuickFormConfig::$ingridPath.'css/ingrid.css" rel="stylesheet" type="text/css" />';
 			$result .="<script type='text/javascript'>
						$(document).ready(
							function() {
								$('#".$this->getTableId()."').ingrid({ 
									url: '".QuickFormConfig::$quickFormBasePath."quickform/ingrid.php?formid=".$this->formId.$isreport."&formMark=".$obj->getFormMark()."&pageRows=".$obj->getPageRows().$this->getParameterString()."',
								    width:'100%',
									initialLoad: false,
									colWidths: [".$width."],	
									sorting: true,
									paging: true,
									totalRecords: ".$obj->getTotalCount().",		
								});
							}
						); 
						</script>";
		   return $result;
 		}
 		public function getDataTableCss($obj)
 		{
 			$result = "<style>";
 			if(!$obj->isAllowWrap()){
 				$result.="th, td { white-space: nowrap;}";
 			}
 			$result.= "div.dataTables_wrapper {width:";
 			$result.= $obj->getTableWidth();
 			$result.= ";margin: 0 auto;}</style>";
 			return $result;
 		}
 		public function getDataTableJs($obj,$loadQuickFormJs=false)
		{
			$isreport = "0";
          	if($obj->isReport())
          	{
          		$isreport = "1";	
          	}
          	$curPage = $obj->getCurPage();
            $startRecord = ($curPage-1)*$pageRows+1;
            if($loadQuickFormJs===true)
            {
          		$result =  $obj->getScriptStr();
          	}
          	$searchStr ="false";
          	if($this->search)
          	{
          		$searchStr ="true";
          	}

			$result .= "<script type='text/javascript'>$(document).ready(function() {

					    $('#".$this->getTableId()."').dataTable( {
					        'bLengthChange':false,
					        'bInfo':false,
					         'bAutoWidth' : true, 
					        'bFilter':".$searchStr.",
					        'processing': true,
					        'serverSide': true,
					        'colReorder': true,
       						'responsive': true,
       						'rowReorder': true, 
					        'pageLength': ".$obj->getPageRows().",
					        'ajax':{
 									   'url':'".QuickFormConfig::$quickFormBasePath."quickform/datatables.php',
 									   'type': 'POST',";
 								if($this->ajaxTimeout>0)
 								{
 									$result .="'timeout':".$this->ajaxTimeout.",";
 								}
 								$result .=	"'data':{
 									   		'json':1,
 									   'formMark':'".$obj->getFormMark()."',
 									   		'searchSign':1,
 									   		'formid':'".$this->formId."',
 									   		'isreport':'".$isreport."',";
 						    foreach($this->parameter as $key=>$value)
				 			{
				 				$result .="'".$key."':'".$value."',";
				 			}
 							$result .= "}
					        		}
					    } );
					} );</script>";
			return $result;
		}

		public function getStatisticHtml($obj,$statisticName,$data=null,$loadQuickFormJs=false)
		{
			 $result = "";
			if($data==null)
 			 {
 			 	$data = $obj->getResult();
 			 }
 			 if($loadQuickFormJs===true)
 			 {
 			 		$result.=$obj->getScriptStr();
 			 }
 			 if($data==null)
 			 {
 			 	$data = $obj->getResult();
 			 }

             $result.= $obj->getStatisticsHtml($statisticName,$data,null);
             if($this->withPanel)
             {
              	 $panel = new HtmlElement();
              	 $panelName = $this->panelName;
              	 if($panelName===null)
              	 {
              	 	if($obj->getStatisticName($statisticName)!=null&&trim($obj->getStatisticName($statisticName))!="")
              	 	{
              	 		 $panelName = $obj->getStatisticName($statisticName);
              	 	}
              	 }
              	 $result = $panel->getPanel($result,$panelName);
             }
             return $result;
		}

		public function getQuickCardHtml($id,$title,$content)
        {
            $div = new HtmlElement($id,$id);
            $div->setParam("class","quickcard");
            $ret = $div->getDiv($content);
             if($this->withPanel)
             {
              	 $panel = new HtmlElement();
              	 $ret = $panel->getPanel($ret,$title);
             }
             return $ret;
        }

		public function getChartHtml($obj,$chartName,$data=null,$loadQuickFormJs=false)
		{
			 $result = "";
 			 if($loadQuickFormJs===true)
 			 {
 			 		$result.=$obj->getScriptStr();
 			 }
 			 if($data==null)
 			 {
 			 	$data = $obj->getResult();
 			 }
             $result.= $obj->getChartHtml($chartName,$data,null);
             if($this->withPanel)
             {
              	 $panel = new HtmlElement();
              	 $panelName = $this->panelName;
              	 if($panelName===null)
              	 {
              	 	if($obj->getChartName($chartName)!=null&&trim($obj->getChartName($chartName))!="")
              	 	{
              	 		 $panelName = $obj->getChartName($chartName);
              	 	}
              	 }
              	 $result = $panel->getPanel($result,$panelName);
             }
             return $result;

		}
        
 		public function getDataTableHtml($obj,$colArray=null,$withTitle=true,$withIngrid=false,$loadJs=true,$loadQuickFormJs=false)
 		{
 			 
 			 $result = "";
 			 if($loadQuickFormJs===true)
 			 {
 			 		$result.=$obj->getScriptStr();
 			 }
 			 $tableid = $this->getTableId();
 			 $divid = $tableid."Div";
 			 	if($loadJs)
 			 	{
 			 		if($withIngrid)
 			 		{
 			 			
 			 			$result .= $this->getIngridJs($obj);
 			 		}
 			 		else
 			 		{

 			 			$result .= $this->getDataTableCss($obj);
 			 			$result .= $this->getDataTableJs($obj,$loadQuickFormJs);
 			 		}
 			 		
 			 	}
 			
 			 $result .= '<div ';
 			 if(!$obj->isAllowWrap())
 			 {
 			 	$result .= 'id= "'.$divid.'" style="position:relative; margin:0px auto; padding:0px;overflow: auto;" ';
 			 }
 			 $result .= '><table width="100%"  id="'.$tableid.'" class="table table-condensed">';
 			
 			 $titleinfo = $obj->getTitleInfo();
 			 $cols = $titleinfo;
 			 if(is_array($colArray)&&count($colArray)>0)
 			 {
 			 	$cols =$colArray;
 			 }
 			 if($withTitle)
 			 {
	 			  $result .= " <thead><tr>";		
	 			        
		          foreach($cols as $dbname =>$title)
		          {
		             $title = $titleinfo[$dbname];        
		             $titleName = $title["name"];    
		       
		             if($title['ischecked'])
		             {
		             
		                $result .= '<th  align="center" width="'.$title['width'].'" style="cursor:hand;'.$title['style'].'">';
		                $result .=htmlspecialchars_decode($titleName);
		                $result .="</th>";
		             }

		           }
		           $result .= "</tr></thead><tbody>";
		           
           		 
			   }
			/* $resultSize = $obj->getResultSize();  
  			 for($j=0;$j<$resultSize;$j++)
		     {
		     	 $result .= "<tr id='". $obj->getMainId($j,"",$export)."'>";
		     	  foreach($titleinfo as $dbname =>$title)
		          {
		          	   $titleName = $title["name"];      
	                   $structure = $obj->getStructureByDbName($dbname);
	                   $style = $structure['style'];
	                   $result .= ' <td align="center" width="'.$title['width'].'" style="'.$style.'" >';
	                   $result .= htmlspecialchars_decode($obj->getValueByDbName($j,$dbname));	
	                   $result .=" </td>";	
		          }	
		     	 $result .= "</tr>";
		     }*/
 			 $result .="</tbody></table></div>"; 

 			 if($withTitle&&!$withIngrid)
 			 {
 			 	$result.= $scriptScr;
 			 }
 			 $result.="<script language='javascript'>
     $('#".$divid."').perfectScrollbar();
     </script>";
              
              if($this->withPanel)
              {
              	 $panel = new HtmlElement();
              	  $panelName = $this->panelName;
              	 if($panelName===null)
              	 {
              	 	if($obj->getFormName()!=null&&trim($obj->getFormName())!="")
              	 	{
              	 		$panelName = $obj->getFormName();
              	 	}
              	 }
              	 $result = $panel->getPanel($result,$panelName);
              }
 			 return $result;
 		}

 	}
?>