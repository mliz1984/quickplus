<?php 
		namespace Quickplus\Lib;
		class QuickStatistics
		{
		    protected $statisticsResult = Array();
		    protected $statisticsTotalResult = Array();
		    protected $statisticTranslateData = Array();
		    protected $statisticColSet = Array();
		    protected $statisticColTitle = Array();
		    protected $statisticShowMethod = Array();
		    protected $statisticCommonParamSet = Array("_statistic_count"=>"statisticsCount");
		    protected $statisticParamSet = Array();
		    protected $defaultStatisticTotalRowName = "Total";
		    protected $statisticTotalRowName = Array();
		    protected $statisticTotalSet = Array();
		    protected $statisticTotal = Array();
		    protected $defaultStatisticTotal = false;
		    protected $statisticCommonTranslateData = Array();
		    protected $cateMethod = Array();
		    protected $summaryColSetting = Array();
		    protected $subTotalSetting = Array();
		    protected $subTitleSetting = Array();
		    protected $statisticResultProcessMethod = Array();
		    protected $showSN = Array();
		    public function setShowSN($setName,$showSN)
		    {
		    	$this->showSN[$setName] =  $showSN;
		    }
		    public function getShowSN($setName)
		    {
		    	$ret = false;
		    	if(is_bool($this->showSN[$setName]))
		    	{
		    		$ret = $this->showSN[$setName];
		    	}
		    	return $ret;
		    }
		    public function setStatisticResultProcessMethod($setName,$method)
		    {	
		    	$this->statisticResultProcessMethod[$setName] = $method;
		    }
		    public function setSubTitle($setname,$col,$setting)
		    {
		    	$this->subTitleSetting[$setname][$col] = $setting; 
		    }
		    public function getSubTitle($setname,$col)
		    {
		    	$result = true;
		    	if(isset($this->subTitleSetting[$setname][$col])&&is_bool($this->subTitleSetting[$setname][$col]))
		    	{
		    		$result = $this->subTitleSetting[$setname][$col];
		    	}
		    	return $result;
		    }
		    public function setSubTotal($setname,$col,$setting)
		    {
		    	$this->subTotalSetting[$setname][$col] = $setting; 
		    }
		    public function getSubTotal($setname,$col)
		    {
		    	$result = true;
		    	if(isset($this->subTotalSetting[$setname][$col])&&is_bool($this->subTotalSetting[$setname][$col]))
		    	{
		    		$result = $this->subTotalSetting[$setname][$col];
		    	}
		    	return $result;
		    }
		    public function setSummaryCol($setname,$col,$setting)
		    {
		    	$this->summaryColSetting[$setname][$col] = $setting;       
		    }
		    public function getSummaryCol($setname,$col)
		    {
		    	$result = false;
		    	if(isset($this->summaryColSetting[$setname][$col])&&is_bool($this->summaryColSetting[$setname][$col]))
		    	{
		    		$result = $this->summaryColSetting[$setname][$col];
		    	}
		    	return $result;
		    }
	        public function setCateMethod($setName,$col,$method)
	        {
	        	$this->cateMethod[$setName][$col] = $method;
	        }
		    public function getCateMethod($setName,$col)
		    {
		    	$ret = null;
		    	if(isset($this->cateMethod[$setName][$col])&&$this->cateMethod[$setName][$col]!=null&&trim($this->cateMethod[$setName][$col])!="")
		    	{
		    		$ret = $this->cateMethod[$setName][$col];
		    	}
		    	return $ret;
		    }

		    public function getCateVal($setName,$data,$col)
		    {
		    	$ret = $data[$col];
		    	$method = $this->getCateMethod($setName,$col);
		    	if($method!=null)
		    	{
		    		$ret = $this->$method($setName,$data,$col);
		    	}
		    	return $ret;
		    }

		    public static  $countSign = "_statistic_count";
		    public function setDefaultStatisticTotal($defaultStatisticTotal)
		    {
		    	$this->defaultStatisticTotal = $defaultStatisticTotal;
		    }
		    public function getStatisticTotal($setname)
		    {
		    	$result = $this->defaultStatisticTotal;
		    	if(is_bool($this->statisticTotal[$setname]))
		    	{
		    		$result = $this->statisticTotal[$setname];
		    	}
		    	return $result;
		    }
		    public function getStatisticTotalRowName($setname)
		    {
		    	$rowname = $this->statisticTotalRowName[$setname];
		    	if($rowname==null||trim($rowname)=="")
		    	{
		    		$rowname = $this->defaultStatisticTotalRowName;
		    	}
		    	return $rowname;
		    }
		 	public function setStatisticTotalRowName($setname,$rowName)
		 	{
		 		$this->statisticTotalRowName[$setname] = $rowName;
		 	}
		    public function setStatisticTotal($setname,$statisticTotal)
		    {
		    	$this->statisticTotal[$setname] = $statisticTotal;
		    }
		    public function setDefaultStatisticTotalRowName($defaultStatisticTotalRowName)
		    {
		    	$this->defaultStatisticTotalRowName = $defaultStatisticTotalRowName;
		    }
		    public function setStatisticParam($setname,$param,$method)
		    {
		    	$this->statisticParamSet[$setname][$param] = $method;
		    }
		    public function setStatisticShowMethod($setName,$col,$showMethod)
		    {
		    	if($showMethod!=null&&trim($showMethod)!="")
		    	{
		    		$this->statisticShowMethod[$setName][$col] = $showMethod;
		    	}
		    }
		    public function setStatisticColTitle($setName,$col,$title)
		    {
		    	if($title!=null&&trim($title)!="")
		    	{
		    		$this->statisticColTitle[$setName][$col] = $title;
		    	}
		    	
		    }
		    public function setStatisticColSet($setName,$colSet)
		    {
		    	$this->statisticColSet[$setName] = $colSet;
		    }
		    public function setStatisticCol($setName,$col,$method,$title=null,$showMethod=null,$totalSet=true)
		    {
		    	$this->statisticColSet[$setName][$col] = $method;
		    	$this->setStatisticColTitle($setName,$col,$title);
		    	$this->setStatisticShowMethod($setName,$col,$showMethod);
		    	if(is_bool($totalSet))
		    	{
			    	if($totalSet)
			    	{
			    	    $this->setStatisticTotalSet($setName,$col,$method);
			    	}
			    	else
			    	{
			    		$this->voidStatisticTotalSet($setName,$col);
			    	}
			    }

		    	
		    }

		    public function voidStatisticTotalSet($setName,$col)
		    {
		    	unset($this->statisticTotalSet[$setName][$col]);
		    }

		    public function setStatisticTotalSet($setName,$col,$method=null)
		    {
		    	if($method!=null&&trim($method)!="")
		    	{
		    		$this->statisticTotalSet[$setName][$col] = $method;
		    	}
		    	
		    }
		    public function addCategoryCols($obj,$spiltBy=",")
		    {
		    	if(is_string($obj))
		    	{
		    		$obj = explode($spiltBy, $obj);
		    	}
		    	if(is_array($obj))
		    	{
		    		$this->categoryCols = array_merge($this->categoryCols,$obj);	
		    	}
		    }

		    public function setCategoryCols($obj,$spiltBy=",")
		    {
		    	if(is_string($obj))
		    	{
		    		$obj = explode($spiltBy, $obj);
		    	}
		    	if(is_array($obj))
		    	{
		    		$this->categoryCols = $obj;	
		    	}
		    	
		    }

		    public function getCatrgoryCols()
		    {
		    	return $this->categoryCols;
		    }

		    public function setStatisticTranslateData($setname,$col,$data,$isAttach=false)
		    {
		    	 $this->statisticTranslateData[$setname][$col]["isAttach"]=$isAttach;
		    	 $this->statisticTranslateData[$setname][$col]["data"]= $data;
		    }

		    public function setStatisticCommonTranslateData($col,$data,$isAttach=false)
		    {
		    	 $this->statisticCommonTranslateData[$setname][$col]["isAttach"]=$isAttach;
		    	  $this->statisticCommonTranslateData[$setname][$col]["data"]=$data;
		    }

		    public function getStatisticTranslateData($setname,$col)
		    {
		    	$result = Array();
		    	if(is_array($this->statisticTranslateData[$setname][$col]))
		    	{
		      		$data = $this->statisticTranslateData[$setname][$col]["data"];
		      		$isAttach = $this->statisticTranslateData[$setname][$col]["isAttach"];
		     
		      		if($isAttach)
		      		{
		      			  $array =  $this->getAttachData($data);
			              $data = Array();
			              foreach($array as $a)
			              {
			                $k = $a["attachdata_id"];
			                $v = $a["attachdata_name"];
			                $data[strval($k)] = strval($v);
			              }
		      		}
		    		$result = $data;
		    	}
		    	else if(is_array($this->statisticCommonTranslateData[$col]))
		    	{
		    		$data = $this->statisticCommonTranslateData[$col]["data"];
		    		$isAttach = $this->statisticCommonTranslateData[$col]["isAttach"];
		    		if($isAttach)
		      		{
		      			  $array =  $this->getAttachData($data);
			              $data = Array();
			              foreach($array as $a)
			              {
			                $k = $a["attachdata_id"];
			                $v = $a["attachdata_name"];
			                $data[strval($k)] = strval($v);
			              }
		      		}
		    		$result = $data;
		    	}
		    	return $result;
		    }



		    protected function getStatisticResult($setname,$key,$value,$template=null)
		    {
		    	    if($template==null&&!is_array($template))
		    	    {
		    	    	$template = Array();
		    	    }
		    	    $totalresult = Array();
		    	    $result = Array();
		    	    if($template==null)
		    	    {
		    			$template = Array();
		    		}

	           		$colmark = $value["statistics_result_col"];
	           		$template[$colmark] = $key;
	             	$display =$key;
	           		if(isset($value["statistics_category"][$colmark."_display"])&&!empty($value["statistics_category"][$colmark."_display"])) 
	           		{
	           			$display = $value["statistics_category"][$colmark."_display"];

	           			
	           		}
	           		$template[$colmark."_display"] = $display;
	           		$tmp = $template;
	           		$tmp["statistics_result_col"]= $colmark ;
	      
	           		$tmp["statistics_result"]= $value["statistics_result"];
	           		$result[$colmark] = $tmp;
	           		

	           		if(is_array($value[0]))
	           		{     

	           			    $tmpTotalResult = $this->getStatisticResult($setname,0,$value[0],$template);
	           			    foreach($tmpTotalResult as $tmpResult)
	           			    {

	           			    	$item = $result;
		           				foreach($tmpResult as $fkey =>$fvalue)
				           		{	
				           			$item[$fkey] = $fvalue;
				           		}
				           		$totalResult[] = $item;
				           	}
	           		}
	           		foreach($value as $k =>$v)
	           		{ 
	           			if($k!="statistics_param" && $k!="statistics_result" && $k!="statistics_result_col")
	           			{	
	           				$tmpTotalResult = $this->getStatisticResult($setname,$k,$v,$template);
	           			    foreach($tmpTotalResult as $tmpResult)
	           			    {
	           			    	$item = $result;
		           				foreach($tmpResult as $fkey =>$fvalue)
				           		{	
				           			$item[$fkey] = $fvalue;
				           		}
				           		$totalResult[] = $item;
				           	}
	           			}
	           		}
	           		if(count($totalResult)==0)
	           		{
	           			$totalResult[] = $result;
	           		}

	           		return $totalResult;
		    }
	        
	        public function  getStatisticCategoryDataList($setname,$xcol,$col,$datalist)
	        {
	        	$order = Array();
	        	$values  = Array();
	        	$data = Array();
	        	$display = Array();
	        	
	        	$dataloop = $datalist[$setname];
	     
	        	foreach($dataloop as $d)
	        	{		
	        		$xval = $d[$xcol];
	        		$val = $d[$col];
	        		$xDisplay = $d[$xcol."_display"]; 
	        		if(!in_array($xval,$order))
	        		{
	        			$order[] = $xval;
	        			$display[] = $xDisplay;
	        		}
	        		if(!in_array($val,$values))
	        		{
	        			$values[] = $val;
	        		}

	        		$data[$xval][$val] = $d;
	        	}
	        	$result = Array("order"=>$order,"values"=>$values,"data"=>$data,"display"=>$display);
	        	return $result;
	        }
	 
	        public function getStatisticDataList($setname,$array,$chartid=null,$spiltBy=null)
	        {
	        	$result = Array();    
	        	
	           $mutilChartMode = false;
	          
	           	    if($chartid!=null&&!empty($spiltBy))
	           	    {
	           	    	 $tmp = explode($spiltBy, $setname);
	           	    	 if(count($tmp)>1)
	           	    	 {
	           	    	 	$mutilChartMode = true;
	           	    	 }
	           	    }
	         
	           	foreach($array as $key=>$value)
	           	{                
	           		 $tmpTotalResult = $this->getStatisticResult($setname,$key,$value);	
	           				$list = Array();
	           			   if($mutilChartMode)
				           {
				           	   
				           	   foreach($array[$key] as $k =>$v)
				           	   {
				           	   		if(!empty($k)&&!in_array($k,Array("statistics_param","statistics_result","statistics_category","statistics_result_col")))
				           	   		{
				           	   			$r = $v["statistics_category"];
				           	   			$r["statistics_result"] = $v["statistics_result"];
				           	   			$result[$setname][]= $r;
				           	   		}
				           	   }
				           	  
				           	}    		
	           				else
	           				{
				           		foreach($tmpTotalResult as $tkey => $tmpResult)
				           	    {
				           	   	        foreach($tmpResult as $fkey =>$fvalue)
						           		{ 	
						           			
						           				$val = $fvalue[$setname];
						           				if(!in_array($val, $list))
						           				{
						           					$result[$fkey][] = $fvalue;
						           					$list[] = $val;
						           				}
						           		}
						              
						        }
						    }
	           			
	           	}
              
	           	
	           	return $result;
	        }
		    public function getStatisticsBySet($setname,$oriArray,$categoryCols,$spiltBy=",",$chartid=null)
		    {
		    	$categoryCols = explode($spiltBy, $categoryCols);

		    	$this->statisticsResult[$setname] = Array();
		    	$colSet = $this->statisticColSet[$setname];
		    	$paramSet = $this->statisticParamSet[$setname];
		    	$commonParamSet = $this->statisticCommonParamSet;
		    	$totalSet = $this->statisticTotalSet[$setname];
		    	$totalCount = Array();
		    	foreach($oriArray as $o)
		    	{	
		    			foreach($paramSet as $param=>$method)
				        {
				    			$newvalue = $this->$method($o,$totalCount[$param]);
				    			$totalCount["statistics_param"][$param] = $newvalue;
				    	}
				    	foreach($commonParamSet as $param=>$method)
				    	{ 

				    			$newvalue = $this->$method($o,$totalCount["statistics_param"][$param]);
				    			$totalCount["statistics_param"][$param] = $newvalue;
				    	}
				    	$params = $totalCount["statistics_param"];
				    	foreach($totalSet as $col=>$method)
				    	{
				    		    $newvalue = $this->$method($o,$totalCount["statistics_result"][$col],$col);
				    		    $totalCount["statistics_result"][$col] = $newvalue;
				    	}
				    	foreach($categoryCols as $categoryCol)
					    {
							 	$newvalue = $o[$categoryCol];
		    				 	$totalCount["statistics_category"][$categoryCol] = $newvalue;
					    }

		    		    $array = $this->statisticsResult[$setname];	
		    		    $tk = $setname;
					    if(!empty($chartid))
					    {
							 $tk = $chartid;
						}

		    			for($j=0;$j<count($categoryCols);$j++)
		    			{
		    				   
		    				 $tmp = $array;
		    				 for($k=0;$k<=$j;$k++)
		    				 { 

		    				 	$tmp = $tmp[$this->getCateVal($tk,$o,$categoryCols[$k])];
		    				 	
		    				 }
		    				 $new = Array();
		    				 foreach($paramSet as $param=>$method)
		    				 {
		    				 	$newparam = $this->$method($o,$tmp["statistics_param"][$param]);
		    				 	$new[$this->getCateVal($tk,$o,$categoryCols[$j])]["statistics_param"][$param] = $newparam;

		    				 }
		    			
		    				foreach($categoryCols as $categoryCol)
						    {
								 	$newvalue = $o[$categoryCol];
								 	$new[$this->getCateVal($tk,$o,$categoryCols[$j])]["statistics_category"][$categoryCol] = $this->getCateVal($tk,$o,$categoryCol);
			    				 	$new[$this->getCateVal($tk,$o,$categoryCols[$j])]["statistics_category"][$categoryCol."_data"] = $newvalue;
						    }
		    				 foreach($commonParamSet as $param=>$method)
		    				 {
		    				 	$newparam = $this->$method($o,$tmp["statistics_param"][$param]);
		    				 	$new[$this->getCateVal($tk,$o,$categoryCols[$j])]["statistics_param"][$param] = $newparam;
		    				 }
		    				
		    				 foreach($colSet as $col=>$method)
		    				 {
		    				 	$newvalue = $this->$method($o,$tmp["statistics_result"][$col],$col);
		    				 	$new[$this->getCateVal($tk,$o,$categoryCols[$j])]["statistics_result"][$col] = $newvalue;

		    				 }
		    				 $new[$this->getCateVal($tk,$o,$categoryCols[$j])]["statistics_result_col"] = $categoryCols[$j];
		    				
		    				 for($a=$j-1;$a>=0;$a--)
		    				 {
		    				 	$new = Array($this->getCateVal($tk,$o,$categoryCols[$a])=>$new);				 	
		    				 }
		    			
		    				 $array =  $this->resultMerge($array,$new);
		    			
		    				
		    	
		    			}
		    		$this->statisticsTotalResult[$setname] = $totalCount;
		    		$this->statisticsResult[$setname] = $array;	
		    	}

		    	return $this->statisticsResult[$setname];
		    }

	        public function getStatisticValue($setname,$col,$value,$dataArray)
	        {
	        	$method = $this->statisticShowMethod[$setname][$col];
	        	if($method!=null&&trim($method)!="")
	        	{
	        		$value = $this->$method($value,$dataArray);
	        	}
	        	return $value;
	        }
		    public function getStatistics($setname,$oriArray,$method,$categoryCols,$spiltBy=",")
		    {
		      	$array = Array("value"=>$method);
		        $this->setStatisticColSet($setname,$array);
		    	return $this->getStatisticsBySet($setname,$oriArray,$categoryCols,$spiltBy);
		    }

		    protected function resultMerge($array,$new)
		    {	

		    	foreach($new as $nk=>$nv)
		    	{
		    		if(is_array($array[$nk]))
		    		{
		    			$tmp = $this->resultMerge($array[$nk],$new[$nk]);

		    			$array[$nk]= $tmp;
		    		}
		    		else
		    		{
		    			$array[$nk] = $nv;
		    		}
		    	}
		    	return $array;
		    }

		    public function statisticsCount($array,$oldvalue,$params=null)
		    {
		    	return intval($oldvalue)+1;
		    } 
	        public function statisticsSum($array,$oldvalue,$params,$isCount=false)
	        {
	        	return $oldvalue + $array[$params];
	        }
	        public function statisticsShow($array,$oldvalue,$params,$isCount=false)
	        {		
  					$ret = $array[$params];
  					return $ret;
	        }
	        public function statisticsDollarShow($value,$dataArray)
	        {
	        	 return "$".number_format($value,2,".","");
	        }
		    public function getStatisticsCount($setname,$oriArray,$categoryCols,$spiltBy=",")
		    {	
		    	
		    	return  $this->getStatistics($setname,$oriArray,"statisticsCount",$categoryCols,$spiltBy);
		    }



		 
		    public function getStatisticsResult($setname)
		    {
		    	return $this->statisticsResult[$setname];
		    }

			
		    public function getTranslateResult($setname,$data,$chartid=null)
		    {
		    	$result = Array(); 
		    //	$result["method"] = $data["method"];
		    	$colSet = $this->statisticColSet[$setname];
		    	$paramSet = $this->statisticParamSet[$setname];
				$commonParamSet = $this->statisticCommonParamSet;
				$tkey = $setname;
				if(!empty($chartid))
				{
					$tkey = $chartid;
				}
		    	foreach($data as $k=>$d)
		    	{ 
		    		$colMark =  $d["statistics_result_col"];
		    		$translateData = $this->getStatisticTranslateData($tkey,$colMark);
		    		$nk = $translateData[$k];
		    		if($nk!=null&&$nk!="")
		    		{
		    			$k = $nk;
		    		}	
		    		foreach($commonParamSet as $col=>$method)
		    		{
		    			$value =  $d["statistics_param"][$col];
		    			$result[$k]["statistics_param"][$col] = $value;
		    		}
		    		foreach($paramSet as $col=>$method)
		    		{
		    			$value =  $d["statistics_param"][$col];
		    			$result[$k]["statistics_param"][$col] = $value;
		    		}
		    	    foreach($colSet as $col=>$method)
		    		{ 
		    			
		    			$tdata = $this->getStatisticTranslateData($tkey,$col);
		    		    $value =  $d["statistics_result"][$col];
		    		    if(count($tdata)>0)
		    		    {
		    		    	$t = $tdata[$value];
		    		    	$value = $t;
		    		    }
		    			$result[$k]["statistics_result"][$col] = $value;
		    		}
		    		$result[$k]["statistics_category"] = $d["statistics_category"];
		    		foreach($d["statistics_category"] as $col=>$value) 
		    		{
		    			$trd = $value;
		    			$td = $this->getStatisticTranslateData($tkey,$col);
		    			
		    			if(is_array($td)&&!empty($td[$value]))
		    			{
		    				$trd = $td[$value];
		    			}

		    			$result[$k]["statistics_category"][$col."_display"] = $trd;
		    		}
		    		$result[$k]["statistics_result_col"] = $colMark;
		    		$setsData = Array();

		    		foreach($d as $dk =>$dd)
		    		{
		    			if(trim($dk)!="statistics_result"&&trim($dk)!="statistics_result_col"&&trim($dk)!="statistics_param"&&trim($dk)!="statistics_category")
		    			{
		    				$t = Array($dk=>$dd);
			    			$tmp = $this->getTranslateResult($setname,$t,$chartid);		
			    			$result[$k] = $this->resultMerge($result[$k],$tmp)	;
			    		}
		    		}
		    	}
		    	
		    	return $result;
		    }


		    
		    public function getStatisticsTable($setname,$array)
		    {
	            ksort($array);
		    	$colSet = $this->statisticColSet[$setname];
		    	$html = "";
		    	$title = "";
		        $total = "";
		    	$titleArray = $this->statisticColTitle[$setname];
		 	    
		    	$showTitle = false;
		    	if(is_array($titleArray)&&count($titleArray)>0)
		    	{
		    		$showTitle = true;
		    	}
		    	$titleMark =false;
		    	$totalData = $this->statisticsTotalResult[$setname]["statistics_result"];
		    	$tmp = "";
		    	if($this->getShowSN($setname))
		    	{
		    		$tmp = "colspan='2'";	
		    	}
		    	$title.="<tr><td align='center' style='vertical-align:middle' ".$tmp." ></td>";
		    	$total.="<tr><td align='center' style='vertical-align:middle'>".$this->getStatisticTotalRowName($setname)."</td>";  
		    	if(!empty($this->statisticResultProcessMethod[$setname]))
		    	{
		    		$method = $this->statisticResultProcessMethod[$setname];
		    		$array = $this->$method($array);

		    	}
		    	$i = 0;
		    	foreach($array as $key => $value)
		    	{ 
		    		$categoryCol = $value["statistics_result_col"];
		    		$categoryValue = $value["statistics_category"][$categoryCol."_display"];
		    		$tmp = "";
		    		$html.="<tr>";
		    		if($this->getShowSN($setname))
		    		{
		    			$i = $i+1;
		    			$html.="<td align='center' style='vertical-align:middle'>".$i."</td>";
		    		}
		    		$html.="<td align='center' style='vertical-align:middle'>".$categoryValue."</td>";
		    		foreach($colSet as $col=>$method)
		    		{

		    			$t = "";
		 
		    			$tmp = $titleArray[$col];

		    			if($tmp!=null&&trim($tmp)!="")
		    			{
		    				$t = $tmp;
		    			}
		    			if(!$this->getSummaryCol($setname,$col))
		    			{
		    				$subtable=$this->getStatisticsSubTable($setname,$value,$col);
		    				$html.=$subtable;
		    			}
		    			if(!$titleMark)
		    			{
			    			$colspan = "";
			    			if(trim($subtable)!=""&&$this->getSubTotal($setname,$col)&&!$this->getSummaryCol($setname,$col))
			    			{
			    				$colspan =" colspan='2' ";
			    			} 
		    				$title.="<td align='center' ".$colspan." style='vertical-align:middle'>".$t."</td>";
		    				$totalValue = "";

		    				if($totalData[$col]!=null&&trim($totalData[$col])!="")
		    				{
		    					$totalValue = $this->getStatisticValue($setname,$col,$totalData[$col],$totalData);
		    				}	
		    				$total.="<td align='center' ".$colspan." style='vertical-align:middle'>".$totalValue."</td>";
		    			}
		    			if($this->getSubTotal($setname,$col))
		    			{
		    				$html.="<td  align='center' style='vertical-align:middle'>".$this->getStatisticValue($setname,$col,$value["statistics_result"][$col],$value["statistics_result"])."</td>";
		    			}
		    		} 
		    		$html.="</tr>";
		    		$titleMark =true;
		    	}
		    	$title.="</tr>";
		    	$total.="</tr>";
		    	
		    	$result ="<table id='quickStat' class='table  table-bordered table-responsive  '>";
		    	if($showTitle)
		    	{
		    		$result.=$title;
		    	}
		    	$result.=$html;
		    	if($this->getStatisticTotal($setname))
		        {
		    			$result.=$total;
		    	}
		    	$result.="</table>";
		    	return $result;
		    }

		    protected function getStatisticsSubTable($setname,$array,$col)
		    {
		    	$html = "";
	                 
		    	if(is_array($array))
		    	{
		    		$newArray = Array();
		    		foreach($array as $key=>$value)
		    		{
		    			if(trim($key)!="statistics_result"&&trim($key)!="statistics_result_col"&&trim($key)!="statistics_param"&&trim($key)!="statistics_category")
		    			{
		    				$value["_key"] = $key;
		    				$newArray[] = $value;
		    			}
		    		}
		    		if(!empty($this->statisticResultProcessMethod[$setname]))
			    	{
			    	
			    		$method = $this->statisticResultProcessMethod[$setname];
			    		$newArray = $this->$method($newArray);

			    	}
			    	foreach($newArray as $newValue)
			    	{
			    		$key= $newValue["_key"];
			    		$value = $array[$key];

			    		if(trim($key)!="statistics_result"&&trim($key)!="statistics_result_col"&&trim($key)!="statistics_param"&&trim($key)!="statistics_category")
			    		{

			    			    $html.="<tr>";
			    			    if($this->getSubTitle($setname,$col))
			    			    { 
			    			    	$categoryMark = $value["statistics_result_col"];
			    			    	$show = $value["statistics_category"][$categoryMark."_display"];	
			    			    	$html.="<td align='center' style='vertical-align:middle'>".$show."</td>";
			    				}
			    				$html.=$this->getStatisticsSubTable($setname,$value,$col);

			    				$html.="<td align='center' style='vertical-align:middle'>".$this->getStatisticValue($setname,$col,$value["statistics_result"][$col],$value["statistics_result"])."</td>";   				
			    			
			    			$html.="</tr>";
			    		}
			    		
			    	}
			    	if(trim($html)!="")
			    	{
			    		$html ="<td><table class='table  table-responsive  table-bordered'>".$html."</table></td>";
			    	}
			    }
		    	return $html;
		    }
		}
	?>