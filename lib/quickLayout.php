<?php
namespace Quickplus\Lib;
use Quickplus\Lib\Tools\HtmlElement;
    
	class quickLayout  extends reportDesignerBase{
		protected $colData = Array();
		protected $subData = Array();
		protected $groupInfo = Array();
		protected $initLayoutMethod = "initLayout";
		protected $isLayout = true;
		protected $setting = Array();
	    protected $showLabelSetting = Array();
	    protected $showGroupLegend = false;
	    protected $stepMode = false;
	    protected $stepNextBtnName = "Next";
	    protected $stepPrevBtnName = "Back";
	    protected $defaultShowLabelSetting = true;
	    protected $stepFormId = "editData";
	    protected $progressBarId = "progress-complete";
	    protected $submitButtonId = null;
	    protected $showProgressBar = true;
	    protected $stepDivHtmlMethod = "showStepDivHtml";
	    protected $stepJsMethod = Array();
	    protected $interval = "<BR>";
	    protected $layoutData = Array();
	    public function initLayoutData($db,$src)
	    {
	    	 return Array();
	    }
	    public function setInterval($interval)
	    {
	    	$this->interval = $interval;
	    }
	    public function setStepJsMethod($stepId,$func,$jsString)
	    {
	    	$func = strtolower($func);
	    	$this->stepJsMethod[$stepId][$func] = $jsString;
	    }
	    public function setStepDivHtmlMethod($setStepDivHtmlMethod)
	    {
	    	$this->setStepDivHtmlMethod = $setStepDivHtmlMethod;
	    }
	    public function openProgressBar()
	    {
	    	$this->showProgressBar = true;
	    }
	    public function closeProgressBar()
	    {
	    	$this->showProgressBar = false;
	    }
	    public function setProgressBarId($progressBarId)
	    {
	    	$this->progressBarId = $progressBarId;
	    }
	    public function setStepNextBtnName($stepNextBtnName)
	    {
	    	$this->stepNextBtnName = $stepNextBtnName;
	    }
	    public function setSetpNextBtnName($stepPrevBtnName)
	    {
	    	$this->stepPrevBtnName = $stepPrevBtnName;
	    }
	    public function setStepFormIdId($stepFormId)
	    {
	    	$this->stepFormId = $stepFormId;
	    }
	    public function setSubmitButtonId($submitButtonId)
	    {
	    	$this->submitButtonId = $submitButtonId;
	    }
	    public function openGroupLengend()
	    {
	    	$this->showGroupLegend = true;
	    }
	    public function closeGroupLengend()
	    {
	    	$this->showGroupLegend = false;
	    }
	    public function setStepMode($stepMode)
	    {
	    	$this->stepMode = $stepMode;
	    }
	    public function setGroupLegend($groupId,$groupLegend)
	    {
	    	$this->groupInfo[$groupId]["legend"] = $groupLegend;
	    }
	    public function getStepJs($formId,$submitButtonId,$js="")
	    {
	    	$result = "";
	    	if($this->stepMode)
	    	{
	    		$result ="<script>
					$('#".$formId."').formToWizard({ submitButton: '".$submitButtonId."',nextBtnClass: 'btn btn-primary next steplink',
                prevBtnClass: 'btn btn-primary prev steplink',showProgress:true,
                nextBtnName: '".$this->stepNextBtnName."',prevBtnName:'".$this->stepPrevBtnName."',";
                if($this->showProgressBar)
                {
                 $result.="progress: function (i, count) {
                    $('#".$this->progressBarId."').width(''+(i/count*100)+'%');
                    $('.stepsdiv').css('color','#b0b1b3');
                    $('#step_progress_'+(i+1)).css('color','#337ab7');
                	},";
             	}
				 $result.="validateBeforeNext: function(form, step) {
                    var stepIsValid = true;
                    var validator = form.validate();
                    $(':input', step).each( function(index) {
                        var xy = validator.element(this);
                        stepIsValid = stepIsValid && (typeof xy == 'undefined' || xy);
                    });
                    return stepIsValid;
                }
                 });";
				$result.=$js;
				$result.="</script>";
	    	}
	    	return $result;
	    }
	    public function exportLayout()
	    {
	    	$result = Array(
	    					"colData" => $this->colData,
	    					"subData" => $this->subData,
	    					"showGroupLegend"=>$this->showGroupLegend,
	    					"stepMode"=>$this->stepMode,
	    					"groupInfo" =>  $this->groupInfo,
	    					"initLayoutMethod" => $this->initLayoutMethod,
	    					"setting" => $this->setting,
	    					"showLabelSetting" => $this->showLabelSetting,
	    					"defaultShowLabelSetting" => $this->defaultShowLabelSetting
	    				   );
	    	return $result;
	    }

	    public function importLayout($layout)
	    {
	    	$this->resetLayout();
	    	$this->colData = $layout["colData"];
		 	$this->subData = $layout["subData"];
		 	$this->groupInfo = $layout["groupInfo"];
			$this->initLayoutMethod = $layout["initLayoutMethod"];
			$this->isLayout = $layout["isLayout"];
		 	$this->setting = $layout["setting"];
		 	$this->showGroupLegend = $layout["showGroupLegend"];
		 	$this->stepMode = $layout["stepMode"];
	     	$this->showLabelSetting = $layout["showLabelSetting"];
	     	$this->defaultShowLabelSetting = $layout["defaultShowLabelSetting"];
	    }

	    public function resetLayout()
	    {
	    	$this->colData = Array();
		 	$this->subData = Array();
		 	$this->groupInfo = Array();
			$this->initLayoutMethod = "initLayout";
			$this->isLayout = true;
		 	$this->setting = Array();
	     	$this->showLabelSetting = Array();
	     	$this->defaultShowLabelSetting = true;
	     	$this->stepMode = false;
	     	$this->showGroupLegend = false;

	    }


		    
	    public function setDefaultShowLabelSetting($showLable)
	    {
	    	$this->defaultShowLabelSetting = $showLable;
	    }

	    public function getDefaultShowLabelSetting()
	    {
	    	return $this->defaultShowLabelSetting;
	    }

	    public function setShowLabelSetting($rowId,$colId,$showLable)
	    {
				$this->showLabelSetting[$rowId][$colId] = $showLable;
	    }

	    public function getShowLableSetting($rowId,$colId)
	    {
	    	$result = $this->getDefaultShowLabelSetting();
	    	if(is_bool($this->showLabelSetting[$rowId][$colId]))
	    	{
	    		$result = $this->showLabelSetting[$rowId][$colId];
	    	}
	    	else if(is_string($this->showLabelSetting[$rowId][$colId])&&$this->showLabelSetting[$rowId][$colId]!=null&&trim($this->showLabelSetting[$rowId][$colId])!="")
	    	{
	    		$result = $this->showLabelSetting[$rowId][$colId];
	    	}
	    	return $result;
	    }



		 protected function getCollapseButton($id,$name,$collapseStr)
		 {

		 	$html = '<button class="btn btn-info  btn-primary btn-xs" role="button" data-toggle="collapse" href="#'.$id.'" aria-expanded="'.$collapseStr.'" aria-controls="'.$id.'">'.$name.'</button>';
		 	return $html;
		 }

		 public function cancelGroupCollapse($groupId)
		 {
		 	$this->setting["group"][$groupId]["collapseMode"] = false;
		 	unset($this->setting["group"][$groupId]["in"]);
		 	unset($this->setting["group"][$groupId]["collapse"]);
		 }

		 public function showStepDivHtml($formId,$totalStep,$step,$legendContent)
		 {
		 	$stepWidth = 12/$totalStep;
		 	$onclickJs ="onClick='_jumpStep(\"".$this->stepFormId."\",\"".$step."\")' style= ";
		 	if(count($this->stepJsMethod)>0)
		 	{
		 		$onclickJs ="style='cursor:auto'";
		 	}
		 	$html.="<div id='step_progress_".$step."' class='col-xs-".$stepWidth." col-md-".$stepWidth." stepsdiv' ".$onclickJs." ><b>Step ".$step."</b>";
		    if($legendContent)
		    {
				   $html.=$this->interval."<div style='font-size:12px'>".$legendContent."</div>";
		    }	
		    $html.=" </div>";
		    $html = StringTools::conv($html,QuickFormConfig::$encode);
		    return $html;
		 }


		 public function getStepJsMethodJs($groupId,$step)
		 {
		 		$result = "";
		 		if(count($this->stepJsMethod[$groupId])>0)
		 		{
		 			$step = $step - 1;		 			
		 			if($step>0&&is_string($this->stepJsMethod[$groupId]["prev"])&&trim($this->stepJsMethod[$groupId]["prev"])!="")
		 			{
		 				$result .='$("#step'.$step.'Prev").bind("click", function(e) {'.$this->stepJsMethod[$groupId]["prev"].'$("#step'.$step.'").hide();$("#step'.($step-1).'").show();selectStep('.($step-1).');return false;});';
		 			}
		 			if(is_string($this->stepJsMethod[$groupId]["next"])&&trim($this->stepJsMethod[$groupId]["next"])!="")
		 			{
		 				$result .='$("#step'.$step.'Next").bind("click", function(e) {';
		 				 
		 				$result .='var validator =$("#'.$this->stepFormId.'").validate();var stepIsValid = true;$(":input", $("#fieldset_step_'.($step+1).'")).each( function(index) {
                            var xy = validator.element(this);
                           stepIsValid = stepIsValid && (typeof xy == "undefined" || xy);
                        });';
		 			   
		 				$result .='if(stepIsValid){'.$this->stepJsMethod[$groupId]["next"].'$("#step'.$step.'").hide();$("#step'.($step+1).'").show();selectStep('.($step+1).');}return false; });';
		 			}
		 		}
		 		return $result;
		 }
		
		 public function setGroupCollapse($groupId,$groupName,$isCollapse,$collapseButton=null)
		 {
		 	$collapseStr = "true";
		 	if($isCollapse)
		 	{
		 		$collapseStr = "false";
		 	}
		 	if($collapseButton==null)
		 	{
		 		$collapseButton = $this->getCollapseButton("div_group_".$groupId,$groupName,$collapseStr);
		 	}
		 	$this->setting["group"][$groupId]["groupName"] =  $groupName;
		 	$this->setting["group"][$groupId]["collapseButton"] = $collapseButton;
		 	$this->setting["group"][$groupId]["class"]["collapse"] = true;
		 	$this->setting["group"][$groupId]["class"]["in"] = !$isCollapse;
		 	$this->setting["group"][$groupId]["collapseMode"] = true;
		 }

  		 public function setIsLayout($isLayout)
  		 {
  		 	$this->isLayout = $isLayout;
  		 }
  		 public function isLayout()
  		 {
  		 	return $this->isLayout;
  		 }
		 public function setInitLayoutMethod($initLayoutMethod)
         {
            $this->initLayoutMethod = $initLayoutMethod;
         }
         public function getInitLayoutMethod($src)
         {
            return $this->initLayoutMethod;
         }
     
        public function getSubmitButton($text,$method)
        {
        	$id = $method;
        	$html = new HtmlElement($id,$id);
        	$js = "_submit('".$method."');";
        	$html->setFunction("onClick",$js);
        	return $html->getSubmit($text);

        }
		public function initLayout($src=null)
		{
			$this->setIsLayout(false);
		}
		public function setHidden($id,$value)
		{
			$this->hidden[$id] = $value;
		}
		protected function setColData($rowId,$colId,$content,$type,$colwidth=null,$align=null,$groupId=null,$custommethod=null)
		{
			$this->groupInfo[$groupId]["enabled"] = true;
			if($groupId==null)
			{
				$groupId = $rowId;
				$this->groupInfo[$groupId]["enabled"] = false;
			}
			
			$this->colData[$groupId][$rowId][$colId] = Array("rowId"=>$rowId,"colId"=>$colId,"content"=>$content,"colwidth"=>$colwidth,"type"=>$type,"align"=>$align,"custommethod"=>$custommethod);
		}	
		public function setColByHtml($rowId,$colId,$htmlstr,$groupId=null,$colwidth=null,$align=null)
		{
			$this->setColData($rowId,$colId,$htmlstr,"html",$colwidth,$align,$groupId);
		}
		public function setColByEditField($rowId,$dbname,$groupId=null,$colwidth=null,$align=null)
		{
			$this->setColData($rowId,$dbname,$dbname,"editfield",$colwidth,$align,$groupId);
		}
		public function setColByEditFieldWithMethod($rowId,$colid,$dbname,$method,$groupId=null,$colwidth=null,$align=null)
		{
			$this->setColData($rowId,$colid,$dbname,"editfield",$colwidth,$align,$groupId,$method);

		}
		public function setColByExtendTable($rowId,$extendTableId,$groupId=null,$colwidth=null,$align=null)
		{
			$this->setColData($rowId,$extendTableId,$extendTableId,"extendTable",$colwidth,$align,$groupId);
		}
		public function setColByEditCustomField($rowId,$id,$groupId=null,$colwidth=null,$align=null)
		{
			$this->setColData($rowId,$id,$id,"editcustomfield",$colwidth,$align,$groupId);
		}
		public function setColByShowField($rowId,$dbname,$custommethod=null,$groupId=null,$colwidth=null,$align=null)
		{
			$this->setColData($rowId,$dbname,$dbname,"showfield",$colwidth,$align,$groupId,$custommethod);
		}
		public function setColBySub($rowId,$id,$colwidth,$groupId=null,$align=null)
		{
			$this->setColData($rowId,$id,$id,"sub",$colwidth,$align,$groupId);
		}
		public function setSubData($subId,$rowId,$colId,$content,$type,$colwidth=null,$align=null,$groupId=null,$custommethod=null)
		{
			if($groupId==null)
			{
				$groupId = $rowId;
			}
			$this->subData[$subId][$groupId][$rowId][$colId] = Array("rowId"=>$rowId,"colId"=>$colId,"content"=>$content,"colwidth"=>$colwidth,"type"=>$type,"align"=>$align,"custommethod"=>$custommethod);
			//print_r($this->subData);
		}
		public function setSubByHtml($subId,$rowId,$colId,$htmlstr,$groupId=null,$colwidth=null,$align=null)
		{
			$this->setSubData($subId,$rowId,$colId,$htmlstr,"html",$colwidth,$align,$groupId);
		}
		public function setSubByEditField($subId,$rowId,$dbname,$groupId=null,$colwidth=null,$align=null)
		{
			$this->setSubData($subId,$rowId,$dbname,$dbname,"editfield",$colwidth,$align,$groupId);
		}
		public function setSubByExtendTable($subId,$rowId,$extendTableId,$custommethod=null,$groupId=null,$colwidth=null,$align=null)
		{
			$this->setSubData($subId,$rowId,$extendTableId,$extendTableId,"extendTable",$colwidth,$align,$groupId,$custommethod);
		}
		public function setSubByShowField($subId,$rowId,$dbname,$custommethod=null,$groupId=null,$colwidth=null,$align=null)
		{
			
			$this->setSubData($subId,$rowId,$dbname,$dbname,"showfield",$colwidth,$align,$groupId,$custommethod);
		}
		public function setSubByEditCustomField($subId,$rowId,$id,$groupId=null,$colwidth=null,$align=null)
		{
			$this->setSubData($subId,$rowId,$id,$id,"editcustomfield",$colwidth,$align,$groupId);
		}
		public function setSubBySub($subId,$rowId,$id,$groupId=null,$colwidth=null,$align=null)
		{
			$this->setSubData($subId,$rowId,$id,$id,"sub",$colwidth,$align,$groupId);
		}

		protected function getAutoColWidth($col)
		{
			$total = 12;
			$nowidth = 0;
			$result = 0;
			foreach($col as $colId => $colInfo)
			{
				$colwidth = $colInfo["colwidth"];
				if($colwidth!=null&&trim($colwidth)!=""&&intval($colwidth)>0)
				{
					$total = 12 - intval($colwidth);
				}
				else 
				{
					$nowidth += 1;
				}
			} 
			if($total>0&&$total>$nowidth)
			{
				$result= intval($total/$nowidth);
			}
			return $result;
		}
		public function getDiv($groupId,$type)
		{
			$result = "";
			$id = "";
			if($type=="group")
			{
				$id = "div_group_".$groupId;
			}
			$div = new HtmlElement($id,$id);

			if($this->setting[$type][$groupId]["collapseMode"])
			{
				$result.='<div class="row"><div class="col-xs-12 col-md-12 control-label form-group">';
				$result.= $this->setting[$type][$groupId]["collapseButton"];
				$result.= '</div></div>';
				$div->setParam("aria-expanded","true");
			}
			$className ="";
			if(is_array($this->setting[$type][$groupId]["class"])&&count($this->setting[$type][$groupId]["class"])>0)
			{
				foreach($this->setting[$type][$groupId]["class"] as $class=>$bool)
				{
					if($bool)
					{
						$className .= " ".$class;
					}
				}
			}
			
			if($className!=null&&trim($className)!="")
			{
				$div->setParam("class",$className);
			}
			$result .= $div->getDiv(null,true);
			return $result;

		}
		public function getHtml($data=null,$width=null,$isSub=false,$src=null)
		{
			$html = "";
			$style = "";
		    
			if($data==null)
			{
				$data = $this->colData;
			}
			 $this->layoutData = $this->initLayoutData($this->getDb(),$src);
			if($width!=null)
		    {
		    	$style = 'style="width:'.$width.'"';
	
		    }
			if(!$isSub)
			{

				$html .= '<div  class="container-fluid" '.$style.'>';
			}
			$progressSign = !$this->showProgressBar;
			$fi = 0;
			$stepJsMethodJs = "";
			foreach($data as $groupId =>$row)
			{	
				
				
				if(!$isSub&&$this->stepMode&&$this->groupInfo[$groupId]["enabled"])
				{
					$html .= $this->getDiv($groupId,"group");
					if(!$progressSign)
			        {
			        	$stepInfo = Array();
			        	foreach($data as $g =>$r)
			        	{
			               if($this->groupInfo[$g]["enabled"])
			               {
			        		 if($this->groupInfo[$g]["legend"]!=null&&trim($this->groupInfo[$g]["legend"])!=null)
			        		 {
			        		 	$stepInfo[$g] = $this->groupInfo[$g]["legend"];
			        		 }
			        		 else
			        		 {
			        		 	$stepInfo[$g] = false;
			        		 }
			        	   }
			        	}
			        	
			        	$stepCount = count($stepInfo);
			            $stepDivHtmlMethod = $this->stepDivHtmlMethod;
			        	$html.="<div  class='col-xs-12 col-md-12 control-label form-group'>";
				        	$sc = 0;
				        	foreach($stepInfo as $sg =>$st)
				        	{ 
				        		$sc ++;
				        		$stepJsMethodJs .= $this->getStepJsMethodJs($sg,$sc);
				        		$html.=$this->$stepDivHtmlMethod($this->stepFormId,$stepCount,$sc,$st);
				        		
				        	}
				        	$html.="</div>";
						$html.=" <div  class='col-xs-12 col-md-12 control-label form-group'><div class='stepprogress'><div id='".$this->progressBarId."' class='stepprogress-complete'></div></div>";
						$progressSign = true;
				    }
				    $fi ++;
					$html.="<fieldset id='fieldset_step_".$fi."' class='quickstep'>";
					
				    if($this->groupInfo[$groupId]["legend"]!=null&&trim($this->groupInfo[$groupId]["legend"])!=null&&$this->showGroupLegend)
				    {
				    	$html.="<legend>".$this->groupInfo[$groupId]["legend"]."</legend>";
				    }
				}
				else
				{
					$html .= $this->getDiv($groupId,"group");
				}
			 	foreach($row as $rowId =>$col)
			 	{
					$html .='<div id="div_row_'.$rowId.' "class="row" style="margin-left:auto">';
					$autoColWidth = $this->getAutoColWidth($col);
					foreach($col as $colId => $colInfo)
					{
						$tmpid = $colId;
						$type = $colInfo["type"];
						if($type == "editfield")
						{
							$tmpid =  $this->getEditPrefix().$colId;
						}
						$divid = $this->getEditdivId($tmpid);
						$colwidth = $colInfo["colwidth"];
						if($colwidth!=null&&trim($colwidth)!=""&&intval($colwidth)>0)
						{
							$colwidth = intval($colwidth);	
						}
						else
						{
							$colwidth = intval($autoColWidth);	
						}
						$html.='<div id="div_col_'.$tmpid.'" class="col-xs-'.$colwidth.' col-md-'.$colwidth.' control-label form-group "';
						$align =  $colInfo["align"];
						if($align!=null&&trim($align)!="")
						{
							$html.= ' align="'.$align.'" ';
						}
						$html.=">";
						$html.=	 $this->getLayoutColHtml($colInfo,$src);
						$html.='</div>';
					}
					$html .='</div>';
				}
				if(!$isSub&&$this->stepMode&&$this->groupInfo[$groupId]["enabled"])
				{
					$html.="</fieldset>";
				}
				$html .='</div>';
			}
			if(!$isSub)
			{
				$html .="</div>";
				if($this->stepMode)
				{
					$html.=$this->getStepJs($this->stepFormId,$this->submitButtonId,$stepJsMethodJs);
				}
			}
			//$html = StringTools::conv($html,QuickFormConfig::$encode);
			return $html;
		}

		public function getTableHtml($data=null,$width=null,$isSub=false,$src=null)
		{
			$html = "";
			$style = "";
		    
			if($data==null)
			{
				$data = $this->colData;
			}
			$this->layoutData = $this->initLayoutData($this->getDb(),$src);
			if($width!=null)
		    {
		    	$style = 'style="width:'.$width.'"';
	
		    }
			
			$html .= '<table class="skipbootstrap" border="0"  '.$style.' >';
			
			
			foreach($data as $groupId =>$row)
			{
				$html .= $this->getDiv($groupId,"group");
				if(!$isSub&&$this->stepMode&&$this->groupInfo[$groupId]["enabled"])
				{
					$html.="<fieldset>";
				    if($this->groupInfo[$groupId]["legend"]!=null&&trim($this->groupInfo[$groupId]["legend"])!=null&&$this->showGroupLegend)
				    {
				    	$html.="<legend>".$this->groupInfo[$groupId]["legend"]."</legend>";
				    }
				}
			 	foreach($row as $rowId =>$col)
			 	{
					$html .='<tr id="tr_row_'.$rowId.'"  >';
					$autoColWidth = $this->getAutoColWidth($col);
					foreach($col as $colId => $colInfo)
					{
						$tmpid = $colId;
						$type = $colInfo["type"];
						if($type == "editfield")
						{
							$tmpid =  $this->getEditPrefix().$colId;
						}
						$divid = $this->getEditdivId($tmpid);
						$colwidth = $colInfo["colwidth"];
						if($colwidth!=null&&trim($colwidth)!=""&&intval($colwidth)>0)
						{
							$colwidth = intval($colwidth);	
						}
						else
						{
							$colwidth = intval($autoColWidth);	
						}
						$html.='<td id="td_col_'.$tmpid.'" style="padding: 2px;" ';
						$align =  $colInfo["align"];
						if($align!=null&&trim($align)!="")
						{
							$html.= ' align="'.$align.'" ';
						}
						$html.=">";
						$html.=	 $this->getLayoutColHtml($colInfo,$src,true);
						$html.='</td>';
					}
					$html .='</tr>';
				}
				if(!$isSub&&$this->stepMode&&$this->groupInfo[$groupId]["enabled"])
				{
					$html.="</fieldset>";
				}
				$html.='</div>';
			}
			
			$html .="</table>";
			if($this->stepMode)
		    {
					$html.=$this->getStepJs($this->stepFormId,$this->submitButtonId);
			}
			$html = StringTools::conv($html,QuickFormConfig::$encode);
			return $html;
		}
		
		public function getLayoutColHtml($colInfo,$src=null,$isTableHtml=false)
		{
			$result = "";
			$type = $colInfo["type"];
			if($type=="html")
			{
				$result = $colInfo["content"];
			}
			else if($type=="sub")
			{
			
				$id =  $colInfo["content"];
				$data = $this->subData[$id];
				if(is_array($data))
				{
					if($isTableHtml)
					{
						$result = $this->getTableHtml($data,null,true,$src);
					}
					else
					{
						$result = $this->getHtml($data,null,true,$src);
					}
				}
			}
			else if($type=="editfield")
			{

			   $dbname =  $colInfo["content"];
               $save = $this->editField[$dbname]["save"];
               $upload = $this->editField[$dbname]["upload"];
      		   $methodname = $this->editField[$dbname]["method"];

      		   if($colInfo["custommethod"]!=null&&trim($colInfo["custommethod"])!="")
      		   {
      		   	  $methodname  = $colInfo["custommethod"];
      		   	 
			   }
			   $rowId = $colInfo["rowId"];
			   $colid = $colInfo["colId"];
			   $showLableSetting = $this->getShowLableSetting($rowId,$colid);
			   $this->setAttr($dbname,"placeholder",$displayname,false,true,false);
			   $result = $this->showEditShowMode($methodname,$save,$upload,$dbname,$src,$this->layoutData[$dbname],false);
			   if($showLableSetting)
			   {
			   		$displayname = $this->fields[$dbname]["displayname"];
			   		if(is_string($showLableSetting))
			   		{
			   			$displayname = $showLableSetting;
			   		}
			   		
			   		$result ='<label style="width:100%;text-align:left" for="'.$this->getEditPrefix().$dbname.'">'.$displayname.'</label>'.$this->interval.$result;
			   }
			}
			else if($type=="editcustomfield")
			{
			   $id =  $colInfo["content"];
			   $value = null;
			   if($src!=null&&is_array($src))
			   {
			   		$value = $src[$id];
			   }
			   $rowId = $colInfo["rowId"];
			   $showLableSetting = $this->getShowLableSetting($rowId,$id);
			 
			   $result = $this->showEditCustomShowModeById($id,true,$value,null,false);
			   if($showLableSetting)
			   {
			   	    $displayname = $this->editCustomField[$id]["name"];
			   	    if(is_string($showLableSetting))
			   		{
			   			$displayname = $showLableSetting;
			   		}
			   		
			   	  
			   		$result =$displayname.$this->interval.$result;
			   }
			}
			else if($type=="extendTable")
			{
				$etId =  $colInfo["content"];
				$dataArray = Array();
				$result = $this->getExtendTableHtml($etId,$dataArray);
				

			}
			else if($type=="showfield")
			{

				 $dbname =  $colInfo["content"];
				 $isExport = $this->isExportMode();
				 $tmp = Array();
				   if($src!=null&&is_array($src))
				   {
				   		$tmp[] = $src;
				   }
				   else
				   {
				   		$tmp[] = Array();
				   }
				   $oriResult = $this->getResult();
				   $this->setResult($tmp);

				   $this->getValueByDbName(0,$dbname);
				   $showLableSetting = $this->getShowLableSetting($rowId,$dbname);
				   $isExport = $this->isExportMode();
				   $custommethod  =  $colInfo["custommethod"];
				   $isCustommethod = false;
				   if($custommethod!=null&&trim($custommethod)!="")
				   {
				   		$isCustommethod = true;
				   }
				   $result =$this->getValueByDbName(0,$dbname,true,$isExport,$isCustommethod,$custommethod);
				   if($showLableSetting)
				   {
				   	$displayname = $this->fields[$dbname]["displayname"];
				   	if(is_string($showLableSetting))
			   		{
			   			$displayname = $showLableSetting;
			   		}
			   		
				     $result =$displayname.$this->interval.$result;
				   }
				   $this->setResult($oriResult);
			}
			return $result;
		}
	}	
?>