<?php
    namespace Quickplus\Lib;
    use Quickplus\Lib\Tools;
	class QuickChart extends QuickStatistics
	{ 
		protected $chartDefaultSetting = Array();
		protected $chartSetting =Array();
		protected $chartLegend = Array();
		protected $chartInfo = Array();
	    protected $chartSetName = Array();
	    protected $chartData = Array();
	    protected $chartSeries = Array();
        protected $chartRenderer = Array();
        protected $chartCustomSetting = Array();
        protected $chartDataProcessMethod = Array();
        protected $chartDataReverse =  Array();
        public function setChartDataReverse($chartid,$isReverse)
        {
            $this->chartDataReverse[$chartid] = $isReverse;
        }
        public function getChartDataReverse($chartid)
        {
            $ret = false;
            if(is_bool($this->chartDataReverse[$chartid]))
            {
                $ret = $this->chartDataReverse[$chartid];
            }
            return $ret;
        }
        public function setChartDataProcessMethod($chartid,$method)
        {
            $this->chartDataProcessMethod[$chartid] = $method;
        }
        public function getChartDataProcessMethod($chartid)
        {
            $ret = null;
            if(isset($this->chartDataProcessMethod[$chartid])&&$this->chartDataProcessMethod[$chartid]!=null&&trim($this->chartDataProcessMethod[$chartid])!="")
            {
                $ret  = trim($this->chartDataProcessMethod[$chartid]);
            }
            return $ret;
        }
        protected function processChartData($oriArray,$chartid,$src)
        {
            $method = $this->getChartDataProcessMethod($chartid);
            if($method!=null)
            {
                $oriArray = $this->$method($chartid,$oriArray,$src);
            }
            return $oriArray;
        }
        public function setChartTooltipFormatter($chartid,$format)
        {
            $this->chartCustomSetting[$chartid]["quickchart_option"]["tooltip"]["formatter"] = $format;
        }
         public function setChartLabelFormatter($chartid,$format)
        {
            $this->chartCustomSetting[$chartid]["quickchart_option"]["label"]['normal']["formatter"] = $format;
        }
        public function setAreaStyle($chartid,$serieName,$style=Array())
        {
            $this->chartCustomSetting[$chartid][$serieName]["areaStyle"] = Array();
        }
        public function getAreaStyle($chartid,$serieName)
        {
            $result = null;
            if(isset($this->chartCustomSetting[$chartid][$serieName]["areaStyle"])&&is_array($this->chartCustomSetting[$chartid][$serieName]["areaStyle"]))
            {
                $result = $this->chartCustomSetting[$chartid][$serieName]["areaStyle"];
            }
            return $result;
        }
        public function setChartRenderer($chartid,$renderer)
        {
            $this->chartRenderer[$chartid] = $renderer;
        }
        public function useSvgRenderer($chartid)
        {
            $this->setChartRenderer($chartid,"svg");
        }
        public function useCanvasRenderer($chartid)
        {
            $this->setChartRenderer($chartid,"canvas");
        }
        public function getChartRenderer($chartid)
        {
            return $this->chartRenderer[$chartid];
        }
	    protected function getChartSetName($cols)
	    {
	    	$added = false;
	    	$result = $cols;

	    	foreach($this->chartSetName as $setName => $setCols)
	    	{
                 
	    		if(StringTools::isStartWith($setCols,$cols))
	    		{
	    			$added = true;
	    			$result = $setName;
	    			break;
	    		}
	    		else if(StringTools::isStartWith($cols,$setCols))
	    		{
	    			unset($this->chartSetName[$setName]);
	    			$this->chartSetName[$result] = $cols;
	    			$added = true;
	    			break;
	    		}

	    	}	
	    	if(!$added)
	    	{
	    		$this->chartSetName[$result] = $cols;
	    	}

	    	return $result;

	    }
	    
        public function getChartData($chartid,$oriArray)
        {
        	$xcol = $this->chartInfo[$chartid]["xcol"];
                         
        	$chartSeries = $this->chartSeries[$chartid];
        	
            $sets = Array();
           
        	foreach($chartSeries as $serieName => $serieData)
        	{
        		$cols = $xcol;
    
        		$spiltBy = $serieData["spiltBy"];
                $ycol = $serieData["ycol"];
        		$method = $serieData["method"];
        		if($ycol!=null&&$ycol!="")
        		{
        			$cols.=$spiltBy.$ycol;
        		}
        		$setName = $this->getChartSetName($cols);
        		$this->chartSeries[$chartid][$serieName]["setName"] = $setName;
        		$sets[$setName][$method] = $method;
             
        	}
           
        	$dataArray = Array();

        	foreach($sets as $setName => $methods)
        	{	
                unset($this->statisticColSet[$setName]);
        		foreach($methods as $method)
        		{
        			$this->setStatisticCol($setName,$method,$method);
        		}
        		$tmpArray = $this->getStatisticsBySet($setName,$oriArray,$setName);

        		$tmpArray = $this->getTranslateResult($setName,$tmpArray);

        		$dataArray[$setName] = $this->getStatisticDataList($setName,$tmpArray);
        	}
            return $dataArray;
        }


         
        protected function getXAxis($chartid)
        {
        	$array = Array();
        	
        	return $array;
        }

        


        protected function getYAxis($chartid)
        {
        	$array = Array();
            $array["type"] = "value";
        	if($this->chartSetting[$chartid]["sign"]["value"]!=null&&trim($this->chartSetting[$chartid]["sign"]["value"])!="")
        	{
        		$value =  $this->chartSetting[$chartid]["sign"]["value"];
        		$isAfter = true;
        		if(is_bool($this->chartSetting[$chartid]["sign"][$isAfter]))
        		{
        			$isAfter = $this->chartSetting[$chartid]["sign"][$isAfter];
        		}
        		if($isAfter)
        		{
        			$value = "{value}".$value;
        		}
        		else 
        		{
        			$value = $value."{value}";
        		}
        		$array["axisLabel"]["formatter"] = $value;
        	}

        	return $array;	
        }

        public function getChartHtml($chartid,$oriArray,$src=null)
        {
            $oriArray = $this->processChartData($oriArray,$chartid,$src);   
            $isReverse = $this->getChartDataReverse($chartid);
            if($isReverse)
            {
                $oriArray = array_reverse($oriArray);
            }
            $chartHtmlArray = $this->getChartHtmlArray($chartid,$oriArray);
            $html ='<div id="div_chart_'.$chartid.'" style="width:'.$this->getChartWidth($chartid).';height:'.$this->getChartHeight($chartid).';"></div>';
            $renderer = $this->chartRenderer[$chartid];
            if($renderer==null||trim($renderer)==""||(trim($renderer)!="svg"&&trim($renderer)!="canvas"))
            {
                $renderer = QuickFormConfig::$defaultChartRenderer;
            }
            $html.= '<script type="text/javascript">';
            $html.= 'var chart_'.$chartid.' = echarts.init(document.getElementById("div_chart_'.$chartid.'"), null, {renderer: \''.$renderer.'\'});';
            $html.= 'var option_'.$chartid.' = {'.jqueryTools::arrayToString($chartHtmlArray).'};';
            $html.= 'chart_'.$chartid.'.setOption(option_'.$chartid.');';
            $html.= '</script>';
            return $html;
        }   
  
  		public function getChartHtmlArray($chartid,$oriArray)
  		{

  			$charttype =$this->chartInfo[$chartid]["charttype"];
  			$isPieChart = false;
  			if($charttype=="pie")
  			{
  					$isPieChart = true;
  			}
  			$result = Array();
  			$dataListArray = $this->getChartData($chartid,$oriArray);
 
  			$result["tooltip"] = $this->getChartTooltipArray($chartid,$isPieChart);
		    $result["toolbox"] = $this->getChartToolBoxArray($chartid,$isPieChart);
		   
		    $xcol  =$this->chartInfo[$chartid]["xcol"];

        	$chartSeries = $this->chartSeries[$chartid];
        	$legend = Array();
        	$xAxisDataStr = "";
        	$xAxisDataMark = false;
        	$serieData = Array();
        	$serieDataValue = Array();
            $serieSetting = Array();
        	foreach($chartSeries as $serieName => $sData)
        	{

        		$group = $sData["group"];
        		$ycol = $sData["ycol"];
        		$setname = $sData["setName"];
        		$spiltBy = $sData["spiltBy"];
        		$addLegend =  $sData["addLegend"];
                $method =   $sData["method"];
                $serieName = $sData["serieName"];
        		$serietype = $charttype;
                $snm = base64_encode($serieName.$method);
        		if($sData["type"]!=""&&trim($sData["type"])!="")
        		{
        			$serietype = $sData["type"];
        		}
        		$pointCol = $xcol;
                
                $multiMode = false;
        		if($ycol!=null&&trim($ycol)!="")
        		{
        			$tmp = explode($spiltBy,$ycol);
        			$pointCol = $tmp[count($tmp)-1];
                    $multiMode = true;
        		}
                $serieSetting[$serieName]["multiMode"] = $multiMode;
                $serieSetting[$serieName]["col"] = $pointCol;
         	   	$array = $dataListArray[$setname][$pointCol];
        	           

                $orderArray = null;
                 
                if(!$multiMode)
                {
              
                    if(!$isPieChart&&$addLegend && !in_array( "'".$serieName."'", $legend))
                    {
                        $legend[] = "'".$serieName."'";
                    }
                   $serieData[$snm]["type"] = $serietype;   
                   $serieData[$snm]["name"] = $serieName;  
                    foreach($array as $a)
        			{  
        				$value = $a[$pointCol];
 						$keyvalue = $a[$xcol];
                        if($isPieChart&&$value!=null&&trim($value)!="")
                        {
                            if($addLegend && !in_array("'".$value."'",$legend))
                            {
                                $legend[] = "'".$value."'";
                            }
                        }

        			   $v = $a["statistics_result"][$method];
                       if($isPieChart)
                       {
                               $serieDataValue[$snm][$value]["value"] = $v;
                               $serieDataValue[$snm][$value]["name"] = $keyvalue;
                       }
                       else
                       {  
                                $serieDataValue[$snm][] = "'".$v."'";

                       }   

        		   }
                  
                }
                else
                {   
                   

                    $array = $this->getStatisticCategoryDataList($setname,$xcol,$ycol,$dataListArray);
                    $orderArray = $array["order"];
                    $valuesArray = $array["values"];
                    $dataArray = $array["data"];
                    $serieSetting[$serieName]["valuesArray"] = $valuesArray;
                    foreach($valuesArray as $va)
                    { 

                        foreach($orderArray as $o)
                        {
                            if($isPieChart&&$o!=null&&trim($o)!="")
                            {
                                if($addLegend && !in_array("'".$o."'", $legend))
                                {

                                    $legend[] = "'".$o."'";
                                }
                            }
                            $v = 0;

                            if($dataArray[$o][$va]["statistics_result"][$method]!=null&&trim($dataArray[$o][$va]["statistics_result"][$method])!="")
                            {
                                $v = $dataArray[$o][$va]["statistics_result"][$method];
                            }
                            if($isPieChart)
                            {
                                if($group)
                                {
                                     $serieDataValue[$snm][$o.$va]["value"] = $v;
                                     $serieDataValue[$snm][$o.$va]["name"] = $o." (".$va.")";
                                }
                                else
                                {
                                   $serieDataValue[$snm.$va][$o]["value"] = $v;
                                   $serieDataValue[$snm.$va][$o]["name"] = $o;
                                }

                            }
                            else
                            {
                                    $serieDataValue[$snm.$va][] = "'".$v."'";
                                    
                            }
                            
                        }
                        if(!$isPieChart&&$addLegend && !in_array("'".$serieName." (".$va.")'", $legend))
                        {
                            $translateData = $this->getStatisticTranslateData($chartid,$ycol);
                            $fva= $va;
                            if(isset($translateData[strval($va)])&&$translateData[strval($va)]!=null)     
                             {
                                    $fva = $translateData[strval($va)];
                             }
                            $legend[] = "'".$serieName." (".$fva.")'";
                        }
                        if($isPieChart&&$group)
                        {
                                 $serieData[$snm]["type"] = $serietype;
                                 $serieData[$snm]["name"] = $serieName;
                        }
                        else
                        {
                            $serieData[$snm.$va]["type"] = $serietype;   
                            $serieData[$snm.$va]["name"] = $serieName." (".$va.")"; 
                        } 
                    }
                   
                }
     
        		if(!$xAxisDataMark)
        		{
        			if($multiMode)
                    {
                        
                        foreach($orderArray as $o)
                        {
                            if($o!=null&&trim($o)!="")
                            {
                                $xAxisDataStr.=",'".$o."'";
                            }
                        }
                    }
                    else
                    {
                        foreach($array as $a)
            			{ 
                           
            				$value = $a[$xcol];
                            
            				if($value!=null&&trim($value)!="")
            				{
            					$xAxisDataStr.=",'".$value."'";
            				}
            			}
                    }
        			$xAxisDataMark = true;
        		}
        		
        	}
            $xAxisDataStr = ltrim( $xAxisDataStr,",");
        	if(COUNT($legend)>0)
        	{
                $result["legend"]["type"] = "scroll";
        		$result["legend"]["data"] = "[".implode(",",$legend)."]";
                $result["legend"]["top"] = "30";
               
        	}
            $tmpStr = "";
            $total = 90;
            $point = 0;
            $serieNum = count($serieData);
          
            $part = intval($total/$serieNum*0.8);
            $skip = intval($total/$serieNum*0.2);
            foreach($chartSeries as $serieName => $sData)
            {  

                $k = $sData["method"];
                $group = $sData["group"];
                $multiMode = $serieSetting[$serieName]["multiMode"];
                $snm = base64_encode($serieName.$method);
                $keyArray = Array();
                if($multiMode)
                {       
                    $valuesArray =  $serieSetting[$serieName]["valuesArray"];
                    $col =  $serieSetting[$serieName]["col"];
                    $translateData = $this->getStatisticTranslateData($chartid,$col);

                    foreach($valuesArray as $v)
                    {
                        $fv = $v;
                        if(isset($translateData[strval($v)])&&$translateData[strval($v)]!=null)     
                        {
                            $fv = $translateData[strval($v)];
                        }
                        $keyArray[] = Array("key"=>$snm.$v,"name"=>$serieName." (".$fv.")");
                    }
                }
                else
                {
                    $keyArray[] = Array("key"=>$snm,"name"=>$serieName);
                }
                foreach($keyArray as $keyData)
                {    
                    $key = $keyData["key"];
                    $dataArray = $serieData[$key];
                    $dataArray["name"] =$keyData["name"];
                  
                    $radiusStr ="";
                    $lableStr = "";
                    $areaStyle = $this->getAreaStyle($chartid,$serieName);
                    if(is_array($areaStyle))
                    {
                        $dataArray["areaStyle"]  = $areaStyle;
                    }
                    if($isPieChart)
                    {

                           $dataStr ="";
                           $tmpCols = Array();
                      
                           foreach($serieDataValue[$key] as $col=>$dArray)
                           {

                                $dataStr.=",{".jqueryTools::arrayToString($dArray)."}";

                           }
                           $dataStr = ltrim($dataStr,",");

                           $dataArray["data"]  = "[".$dataStr."]";
                           $lable = Array();
                             if(isset($this->chartCustomSetting[$chartid]["quickchart_option"]["label"])&&is_array($this->chartCustomSetting[$chartid]["quickchart_option"]["label"]))
                                    {
                                        foreach($this->chartCustomSetting[$chartid]["quickchart_option"]["label"] as $k => $v)
                                        {
                                            $lable["label"][$k] = $v;
                                        }
                                    }
                           if($serieNum>1)
                           {
                                $radiusStr = "radius:['".$point."%','".($point+$part)."%'],";
                            
                                $lable["label"]["normal "]["show"]= false;
                                
                           }
                           $lableStr = jqueryTools::arrayToString($lable);
                           
                           
                           if(trim($lableStr)!="")
                           {
                            $lableStr.=",";
                           }
                           
                           $point = $point + $part + $skip;
                    }
                    else
                    {

                           if($group)
                           {
                                 $groupValue = $group;
                                 if(is_bool($group))
                                 {
                                    $groupValue = $serieName;
                                 }
                                 $dataArray["stack"] = $groupValue;  
                           }
                	       $dataArray["data"] = "[".implode(",",$serieDataValue[$key])."]";   
                        
                    }

                    $tmpStr .=",{".$lableStr.$radiusStr.jqueryTools::arrayToString($dataArray)."}";
              
                }
            }
            $tmpStr = ltrim($tmpStr,",");
            $lableStr = "";
            if($isPieChart&&$serieNum>1)
            {
                
            }
            $result["series"] = "[".$tmpStr."]";
        	if(!$isPieChart)
		    {

		    	$result["yAxis"] = $this->getYAxis($chartid);
		    	$xAxis = $this->getXAxis($chartid);
		    	if($xAxisDataMark!=null&&trim($xAxisDataMark)!="")
	        	{
	        		$xAxis["data"] = "[".$xAxisDataStr."]";
	        	}
	        	$result["xAxis"] = $xAxis;
		    }
            return $result;
  		}

        
        protected  function getChartToolBoxArray($chartid,$isPieChart=false)
        {
        	$array = Array();
        	$array["show"] = true;
        	if($this->chartRenderer[$chartid]!="svg")
            {    
                $array["feature"]["saveAsImage"]["title"] = "Save as image";
                $array["feature"]["saveAsImage"]["type"] = "jpeg";
            }
            $array["feature"]["dataView"]["show"] = "Data View";
            //$array["feature"]["saveAsImage"]["title"] = "Save as image";
        	if(!$isPieChart)
			{
			
            	//$array["feature"]["dataZoom"]["title"]["zoom"] = "Area Zoom";
				//$array["feature"]["dataZoom"]["title"]["back"] = "Undo Area Zoom";  
        		$array["feature"]["magicType"]["type"] = "['line', 'bar','stack','tiled']";
        		$array["feature"]["magicType"]["title"]["line"] = "Line view";
        		$array["feature"]["magicType"]["title"]["bar"] = "Bar view";
        		$array["feature"]["magicType"]["title"]["stack"] = "Stack view";
        		$array["feature"]["magicType"]["title"]["tiled"] = "Tiled view";
                $array["feature"]["restore"]["title"] = "Restore";
            }
           
             return $array;
        }
        protected  function getChartTitleArray($chartid)
        {
        	return $this->chartSetting[$chartid]["title"];
        }
        protected function getChartTooltipArray($chartid,$isPieChart=false)
        {
        	$value = "axis";
        	if($isPieChart)
        	{
        		$value = "item";
        	}
            if(isset($this->chartCustomSetting[$chartid]["quickchart_option"]["tooltip"])&&is_array($this->chartCustomSetting[$chartid]["quickchart_option"]["tooltip"]))
            {
                foreach($this->chartCustomSetting[$chartid]["quickchart_option"]["tooltip"] as $k => $v)
                {
                    $array[$k] = $v;
                }
            }
        	$array["trigger"] = $value;
        	return $array; 
        }
        public function setChartSign($sign,$isAfter=true)
        {
        	$this->chartSetting[$chartid]["sign"]["value"] = $sign;
        	$this->chartSetting[$chartid]["sign"]["isAfter"] = $isAfter;
        }
		public function setChartInfo($charttype,$chartid,$xcol)
		{
			$this->chartInfo[$chartid] = Array("xcol"=>$xcol,"charttype"=>$charttype);
		}

		public function setChartSerie($chartid,$serieName,$method,$ycol=null,$group=false,$addLegend=true,$type=null,$spiltBy=",")
		{
			$this->chartSeries[$chartid][$serieName] = Array("serieName"=>$serieName,"ycol"=>$ycol,"method"=>$method,"group"=>$group,"addLegend"=>$addLegend,"spiltBy"=>$spiltBy,"type"=>$type);
		}

		public function setChartSetting($chartid,$param,$value)
		{
			$this->chartSetting[$chartid][$param] = $value; 
		}
		public function setChartDefaultSetting($param,$value)
		{
			$this->chartDefaultSetting[$param] = $value; 
		}

		public function setChartTitle($chartid,$title)
		{
			$this->chartSetting[$chartid]["title"]["text"] = $title;
		}

		public function setChartSubTitle($chartid,$title)
		{
			$this->chartSetting[$chartid]["title"]["subtext"] = $title;
		}

        public function getChartWidth($chartid)
        {
            $result = QuickFormConfig::$defaultChartWidth;
            if($this->chartSetting[$chartid]["width"]!=null&&trim($this->chartSetting[$chartid]["width"])!="")
            {
                 $result = $this->chartSetting[$chartid]["width"];
            }
            return $result;
        }

         public function getChartHeight($chartid)
        {
            $result = QuickFormConfig::$defaultChartHeight;
            if($this->chartSetting[$chartid]["height"]!=null&&trim($this->chartSetting[$chartid]["width"])!="")
            {
                 $result = $this->chartSetting[$chartid]["height"];
            }
             return $result;
        }

		public function setChartWidth($chartid,$width)
		{
			$this->chartSetting[$chartid]["width"] = $width;
		}

		public function setChartHeight($chartid,$height)
		{
			$this->chartSetting[$chartid]["height"] = $height;
		}

		protected function getChartSettingValue($chartid,$param)
		{
			$result = null;
		    if(isset($this->chartSetting[$chartid][$param]))
		    {
		    	$result = $this->chartSetting[$chartid][$param];
		    }
		    elseif(isset($this->chartDefaultSetting[$param]))
		    {
		    	$result = $this->chartDefaultSetting[$param];
		    }
		    return $result;
		}
	}
?>