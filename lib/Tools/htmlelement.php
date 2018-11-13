<?php
namespace  Quickplus\Lib\Tools;
use \Quickplus\Lib\QuickFormConfig as QuickFormConfig;

    Class HtmlElement{
        protected $id;
        protected $name;
        protected $param;
        protected $function;
        protected $interval = "\"";

            function __construct($id=null,$name=null)
            {
                $this->id = $id;
                $this->name = $name;
                $this->param = Array();
                $this->function = Array(); 
            }
            public function setInterval($interval)
            {
               $this->interval = $interval; 
            }
            public function getInterval()
            {
              return  $this->interval;
            }
            public function setId($id)
            {
                $this->id = $id;
            } 
            public function getId()
            {
                return $this->id;
            } 
            public function setName($name)
            {
                $this->name = $name;
            } 
            public function getName()
            {
                return $this->name;
            } 
            public function setParam($key,$value=null)
            {
                $this->param[$key] = $value;
               
            }

            public function setParams($params)
            {
                $this->param = $params;
            }



            public function clearParam()
            {
                $this->param = array();
            }

            public function setFunctions($functions)
            {
                 $this->function = $functions;
            }
            public function setFunction($key,$value)
            {
                $this->function[$key] = $value;
            }
            public function clearFunction()
            {
                $this->function = array();
            }
            public function removeParam($key)
            {
                unset($this->param[$key]);
            }
            public function removeFunction($key)
            {
                unset($this->function[$key]);
            }
            public function getParams()
            {
               return $this->param;
            }
            private function getParamString()
            {
                $result = "";
                $this->setParam("id", $this->id);
                $this->setParam("name", $this->name);
                foreach($this->param as $key=>$value)
                {
                   if($value!=null&&trim($value)!="")
                  {
                    $result .=" ".$key."=".$this->interval.$value.$this->interval;
                  }
                  else
                  {
                     $result .=" ".$key." ";
                  }
                }
                return $result;
            }
            public function getFunctionString()
            {
                $result = "";
                foreach($this->function as $key=>$value)
                {
                  if($value!=null&&trim($value)!="")
                  {
                    $result .=" ".$key."=".$this->interval.$value.$this->interval;
                  }
                }
                return $result;
            }
            public function getHtmlElement($head="",$main="")
            {
                $result = $head." ";
                $result.= $this->getParamString();
                $result.= " ".$main." ";    
                $result.= $this->getFunctionString();
                $result.=" / >"; 
                return $result;                                               
            }
            public function getRadiosWithHiddenByList($list,$idKey,$valueKey,$nowvalue=null,$intervalBy="<br/>")
            {
               $array = Array();
               foreach($list as $data)
               {
                  $key = $data[$idKey];
                  $value = $data[$valueKey];
                  $array[$key] = $value;
               }
               return $this->getRadiosWithHidden($array,$nowvalue,$intervalBy);
            }
            public function getRadiosWithHidden($array,$nowvalue=null,$intervalBy="<br/>",$desc=false)
            {
               $html = $this->getHidden($nowvalue);
               $radioHtml = "";
               $hiddenid = $this->getId();
               $hiddenname = $this->getName();
               $radioname= $hiddenname;
               if($radioname==null||trim($radioname)=="")
               {
                 $radioname = $hiddenid;
               }
               $addIntervalBy = false;
               $onClick = $this->function["onClick"];
               if($onClick==null)
               {
                  $onClick = "";
               }
               $onClick = "document.getElementById('".$hiddenid."').value = this.value ;".$onClick;     
               $this->setFunction("onClick",$onClick);
              
               foreach($array as $value =>$text)
               {
                  if($desc)
                  {
                     $tmp = $value;
                     $value = $text;
                     $text = $tmp;
                  } 
                  
                  
                  $this->setId("");
                  $this->setName($radioname);
                 
                  if($addIntervalBy)
                  {
                    $radioHtml .=$intervalBy;
                  } 
                  else
                  {
                    $addIntervalBy = true;
                  }

                  $radioHtml .= $this->getRadio($value,$nowvalue)."&nbsp;".$text;
               }
               $html.= $radioHtml;
               return $html;
            }
            public function getRadio($value,$nowvalue=null)
            {
                $head = "<input type=\"radio\" ";
                $main = " value=\"".$value."\" ";
                if($nowvalue!=null&&strval($value) == strval($nowvalue))
                {

                    $main .= " checked ";
                }
                return $this->getHtmlElement($head,$main);
            }
            
            public function getPasswordInput($value="")
            {
                $head = "<input type=\"password\" ";
                $$main = "";
                if($value!=null&&trim($value)!="")
                {
                  $main = " value=\"".$value."\" ";
                }
                return $this->getHtmlElement($head,$main);
            }
           
            public function getNumber($value="",$numberFormatSetting=null)
            {
                $head = "<input type=\"number\" ";
                $main = "";
                if($value!=null&&trim($value)!="")
                {
                  $main = " value=\"".$value."\" ";
                }
                if(is_array($numberFormatSetting))
                {
                	foreach($numberFormatSetting as $k => $v)
                	{
                		if($v!==null&&trim($v)!="")
                		{
                			$head.=" ".$k."=\"".$v."\" "; 
                		}
                	}
                }

                return $this->getHtmlElement($head,$main);
            }

            public function getInput($value="")
            {
                $head = "<input type=\"text\" ";
                $main = "";
                if($value!=null&&trim($value)!="")
                {
                  $main = " value=\"".$value."\" ";
                }
                return $this->getHtmlElement($head,$main);
            }
            public function getButton($value)
            {
                $head ="<input type=\"button\" ";
                $main = "";
                if($value!=null&&trim($value)!="")
                {
                  $main = " value=\"".$value."\" ";
                }
                return $this->getHtmlElement($head,$main);
            }
            public function getSubmit($value="Submit")
            {
                $head = "<input type=\"submit\" ";
                 $main = "";
                if($value!=null&&trim($value)!="")
                {
                  $main = " value=\"".$value."\" ";
                }
                return $this->getHtmlElement($head,$main);
            }
            public function getHidden($value)
            {
                $head = "<input type=\"hidden\" ";
                $main = "";
                if($value!=null&&trim($value)!="")
                {
                   $main = "";
                  if($value!=null&&trim($value)!="")
                  {
                    $main = " value=\"".$value."\" ";
                  }
                }
                 return $this->getHtmlElement($head,$main);
            } 
            public function getDiv($html=null,$justStart=false)
            {
                $result = "<div ";
                $result.= $this->getParamString();
                $result.=" ";
                $result.= $this->getFunctionString();
                $result.=" >";
                if($html!=null&&trim($html)!="")
                {
                    $result .= $html;
                }
                if(!$justStart)
                {
                  $result.="</div>";
                }
                return $result;
            }
            public function getFile()
            {
                 $head = "<input type=\"file\" ";
                 $main = " ";
                 return $this->getHtmlElement($head,$main);
            }

            public function getUrl($show,$url,$target="_self")
            {
                $this->setParam("target",$target);
                $result = "<a ";
                $result.= $this->getParamString();
                $result.= "href='".$url."' ";
                $result.= $this->getFunctionString();
                $result.=" >".$show."</a>";
                return $result;
            }
            public function getImg($url)
            {
              $head = "<img ";
              $this->setParam("src",$url);
              return $this->getHtmlElement($head,$main);  
            }
            public function getSelectByDataMsg($dataMsg,$valueKey,$textKey,$chooseValue="",$paramsKey=null,$defaultTitle=null,$defaultValue="",$mustSelected=true)
            {
                   
                if($paramsKey==null)
                {
                    $paramsKey = Array();
                }
                $result = "<select ";
                $result.= $this->getParamString();
                $result.= $this->getFunctionString();
                $result.=" >";
                if($defaultTitle!=null)
                {
                    $result.= "<option value=\"".$defaultValue."\"";
                    $result.=" selected ";
                    $result.=">".$defaultTitle."</option>"; 
                }
             
                for($i=0;$i<$dataMsg->getSize();$i++)
                {
                    $data = $dataMsg->getData($i);
                    $value = $data->get($valueKey);
                    $text = $data->get($textKey);
                    $result.= "<option value=\"".$value."\"";
                    foreach($paramsKey as $key => $value2)
                    {
             
                        $result.= " ".$key."=\"". $data->get($value2)."\" ";
                    }
                     if(($i==0&&$defaultTitle==null&&$mustSelected)||strval($chooseValue)==strval($value)||in_array(strval($value), $chooseValue))
                    {
                        $result.=" selected ";
                    }
                    $result.=">".$text."</option>"; 
                }
                 $result.="</select>"; 
                return $result;
                
            }
           public function getSelectByMap($array,$textKey,$chooseValue,$defaultTitle=null,$defaultValue="",$mustSelected=true)
           {
                $result = "<select ";
                $result.= $this->getParamString();
                $result.= $this->getFunctionString();
                $result.=" >";
                if($defaultTitle!=null)
                {
                    $result.= "<option value=\"".$defaultValue."\"";
                    $result.=" selected ";
                    $result.=">".$defaultTitle."</option>"; 
                }
                $i =0;
                foreach($array as $value=>$data)
                {
                    
                    $text = $data[$textKey];
                    $result.= "<option value=\"".$value."\"";
                    if(($i==0&&$defaultTitle==null&&$mustSelected)||strval($chooseValue)==strval($value)||in_array(strval($value), $chooseValue))
                    {
                        $result.=" selected ";
                    }
                    $result.=">".$text."</option>"; 
                    $i++;
                }
                return $result;
           }

           public function getSelectByChoosedArray($array,$valueKey,$textKey,$chooseKey,$chooseValue = "1",$defaultTitle=null,$defaultValue="",$mustSelected=true)
           {
              $choosed = "";
              foreach($array as $data)
              {
                 if(strval($data[$chooseKey])==$chooseValue)
                 {
                    $choosed = $data[$valueKey];
                    break;
                 }
              }
              $result =  $this->getSelectByArray($array,$valueKey,$textKey,$choosed,$defaultTitle,$defaultValue);
              return $result;
           } 

           public function getSelectByArray($array,$valueKey,$textKey,$chooseValue="",$defaultTitle=null,$defaultValue="",$mustSelected=true)
           {
                
                $result ="<select ";
                $result.= $this->getParamString();
                $result.= $this->getFunctionString();
                $result.=" >";
                if($defaultTitle!=null)
                {
                    $result.= "<option value=\"".$defaultValue."\"";     
                    $result.=" selected ";
                    $result.=">".$defaultTitle."</option>"; 
                }
                for($i=0;$i<count($array);$i++)
                {
                    $data = $array[$i];
                    $value = $data[$valueKey];
                    $text = $data[$textKey];
                    $result.= "<option value=\"".$value."\"";
                    
                    if(($i==0&&$defaultTitle==null&&$mustSelected)||strval($chooseValue)==strval($value)||in_array(strval($value), $chooseValue))
                    {
                        $result.=" selected ";
                    }
                    $result.=">".$text."</option>"; 
                }
                $result.="</select>"; 
                return $result;
           }
             

            
            public function getSelect($list,$chooseValue=null,$desc=false,$defaultTitle=null,$defaultValue="",$mustSelected=true)
            {
                $result = "<select ";
                 $result.= $this->getParamString();
                $result.= $this->getFunctionString();
                $result.=" >";
                if($defaultTitle!=null)
                {
                    $result.= "<option value=\"".$defaultValue."\"";
                    $result.=" selected ";
                    $result.=">".$defaultTitle."</option>"; 
                }
                $i = 0;
                foreach($list as $key=>$value)
                {
                    $akey = $key;
                    $avalue = $value;
                    if($desc)
                    {
                        $akey = $value;
                        $avalue = $key; 
                    }
                    $result.= "<option value=\"".$avalue."\"";   

                   if(($i==0&&$defaultTitle==null&&$mustSelected)||strval($chooseValue)==strval($avalue)||in_array(strval($avalue), $chooseValue))
                    {
                        $result.=" selected ";
                    }
                    $result.=">".$akey."</option>";
                    $i++;
                }
                $result.="</select>"; 
                
                return $result;                  
            }
            public function getReset($value)
            {
                $head ="<input type=\"reset\" ";
                $main = "";
                if($value!=null&&trim($value)!="")
                {
                  $main = " value=\"".$value."\" ";
                }
                return $this->getHtmlElement($head,$main);
            }
            
            public function getTextArea($value,$rows=null,$cols=null)
            {
                  if($rows!=null)
                  {
                       $this->setParam("rows", $rows);
                  }
                  if($cols!=null)
                  {
                       $this->setParam("cols", $cols);
                  }
                  $result = "<textarea ";
                  $result.= $this->getParamString();
                  $result.= $this->getFunctionString();  
                  $result.=" >";
                  $result.=$value;
                  $result.="</textarea>";
                  return  $result;    
            }

            public function getSwitch($value=null,$nowvalue=null)
            {
              $html = $this->getCheckBox($value,$nowvalue,true);
              return $html;
            }
            
            public function getCheckBox($value=null,$nowvalue=null,$isSwitch=false)
            {
                $result ="<input type=\"checkbox\" ";
                 if($isSwitch)
                 {
                    $this->setParam("BootstrapSwitch","1");
                 }
                $result.= $this->getParamString();
                if($value!=null)
                {
                    $result .=" value='".$value."' ";
                }
                if($nowvalue!=null&&$value == $nowvalue)
                {
                   $result.=" checked ";
                }     
                $result.= $this->getFunctionString();     
                $result .=" />"; 
                return $result;       
            }
             public function getSwitchWithHidden($nowvalue=null,$checkedvalue=1,$uncheckedvalue=0,$onChange=null,$switchOnText=null,$switchOffText=null)
            {

              $html = $this->getCheckBoxWithHidden($nowvalue,$checkedvalue,$uncheckedvalue,$onChange,true,$switchOnText,$switchOffText);
              return $html;
            }

            public function getCheckBoxWithHidden($nowvalue=null,$checkedvalue=1,$uncheckedvalue=0,$onChange=null,$isSwitch=false,$switchOnText=null,$switchOffText=null)
            { 
                 $value = $uncheckedvalue;
                 if($nowvalue == $checkedvalue)
                 {
                    $value = $checkedvalue;
                 }
                 $markNum = StringTools::getRandStr();
                 $this->setParam("marknum",$markNum);
                 $result  = $this->getHidden(strval($value));
                 $id = "chk_".$this->id;
                 $name = $this->name;
                 if($name!=null&&trim($name)!=null)
                 {
                    $name = "chk_".$name;
                 }
                 $checkBox = new HtmlElement($id,$name);
                  
                 $checkBox->setFunctions($this->function);
                  $this->setParam("markNum","");
                 $checkBox->setParams($this->param);
                 if($switchOnText!=null&&trim($switchOnText)!="")
                 {
                    $checkBox->setParam("data-on-text",$switchOnText);
                 }
                 if($switchOffText!=null&&trim($switchOffText)!="")
                 {
                     $checkBox->setParam("data-off-text",$switchOffText);
                 }
                
                 $this->clearFunction();
                 $this->clearParam();
                 $js = "if(this.checked){\$('[marknum=".$markNum."]').val('".$checkedvalue."');} 
                        else{\$('[marknum=".$markNum."]').val('".$uncheckedvalue."');}";
                 
                 if($onChange!=null)
                 {
                    $js.=$onChange;
                 }
                 $checkBox->setFunction("onChange",$js);
                 if($isSwitch)
                 {
                    $checkBox->setParam("BootstrapSwitch","1");
                 }
                 $checkBox->setParam("checkboxWithhidden","1");
                 $result.=$checkBox->getCheckBox($checkedvalue,$nowvalue);
                 return $result;
            }
               public function getCheckBoxesByArray($array,$ids=null,$desc=false,$spilt=",",$withAll=true)
               {
                 return  $this->getCheckBoxsByArray($array,$ids,$desc,$spilt,$withAll);
               }
            
              public function getCheckBoxsByArray($array,$ids=null,$desc=false,$spilt=",",$withAll=true)
              {


                 $idArray = Array();
                
                 if($ids!=null&&trim($ids)!="")
                 {
                     $temp = explode($spilt,$ids);
                     for($i=0;$i<count($temp);$i++)
                     {
                         $id = strval($temp[$i]);
                         $idArray[$id] = $id;
                     }

                 }
                 
                 $resultArray = Array();
                 if($withAll)
                 {
                      $oriId  = $this->id;
                      $oriName = $this->name;
                      $objId = substr($oriId,0,strlen($oriId)-2);
                      $allId = $objId."_all";
                      $this->id = $allId;
                      $this->name = $allId;
                      $result ="<input type=\"checkbox\" ";
                      $result.= $this->getParamString();
                      $result.= " onClick=\"_chooseAllCheckBoxes(this,'".$objId."')\"";
                      $result.=" />";     
                      $this->id = $oriId;
                      $this->name = $oriName;
                      $data = Array(
                        "echo"=>"All",
                        "checkBox"=>$result,
                      );
                      $resultArray[] = $data;
                 }
                 foreach($array as $echo =>$value)
                 {
                
                    if($desc)
                    {
                      $temp = $echo;
                      $echo = $value;
                      $value = $temp;
                    }
                    $result ="<input type=\"checkbox\" ";
                    $result.= $this->getParamString();
                    $result .=" value='".$value."' ";
                  
                    $point = ArrayTools::getValueFromArray($idArray,$value);

                    if($point!=null&&strval($value) == strval($point))
                    {
                       $result.=" checked ";
                    }     
                    $result.= $this->getFunctionString();     
                    $result .=" />";     
                    $data = Array(
                        "echo"=>$echo,
                        "checkBox"=>$result,
                    );
                    $resultArray[] = $data;
                 }
                 return $resultArray;
                 
            }
            public function getCheckBoxes($array,$valueKey,$echoKey,$ids=null,$spilt=",")
            {
              return  $this->getCheckBoxs($array,$valueKey,$echoKey,$ids,$spilt);
            }
            
            public function getCheckBoxs($array,$valueKey,$echoKey,$ids=null,$spilt=",")
            {
                 $idArray = Array();
                 if($ids!=null&&trim($ids)!="")
                 {
                     $temp = explode($spilt,$ids);
                     for($i=0;$i<count($temp);$i++)
                     {
                         $id = strVal($temp[$i]);
                         $idArray[$id] = $id;
                     }
                 }
                 $resultArray = Array();
                  if($withAll)
                 {
                      $oriId  = $this->id;
                      $oriName = $this->name;
                      $objId = substr($oriId,0,strlen($oriId)-2);
                      $allId = $objId."_all";
                      $this->id = $allId;
                      $this->name = $allId;
                      $result ="<input type=\"checkbox\" ";
                      $result.= $this->getParamString();
                      $result.= " onClick=\"_chooseAllCheckBoxes(this,'".$objId."')\"";
                      $result.=" />";     
                      $this->id = $oriId;
                      $this->name = $oriName;
                      $data = Array(
                        "echo"=>"All",
                        "checkBox"=>$result,
                      );
                      $resultArray[] = $data;
                 }
                 for($i=0;$i<count($array);$i++)
                 {

                    $echo = strVal($array[$i][$echoKey]);
       
                    $value = strVal($array[$i][$valueKey]);

                    $result ="<input type=\"checkbox\" ";
                    $result.= $this->getParamString();
                    if($value!=null)
                    {
                        $result .=" value='".$value."' ";
                    }
                    $point = $idArray[$value];

                    if($point!=null&&$value == $point)
                    {
                       $result.=" checked ";
                    }     
                    $result.= $this->getFunctionString();     
                    $result .=" />";     
                    $data = Array(
                        "echo"=>$echo,
                        "checkBox"=>$result,
                    );
                    $resultArray[] = $data;
                 }
                 return $resultArray;
                 
            }
            
              public function getCheckBoxsByDataMsg($dataMsg,$valueKey,$echoKey,$ids=null,$spilt=",")
              {
                  return $this->getCheckBoxs($dataMsg->getDataArray(),$valueKey,$echoKey,$ids,$spilt);
              }


              public function getCascadeSelect($db,$sql,$topMark="0",$chooseValue=null,$pre="",$defaultText="Please choose...",$defaultValue="",$extend=null,$methodName=null)
              {
                     
                      $hasRoot = false;
                      if($extend!=null&&$methodName!=null&&trim($extend)!=""&&trim($methodName)!="")
                      { 
                          require_once(FileTools::connectPath(FileTools::getRealPath(QuickFormConfig::$htmlExtend),$extend).".php");     
                          $rootArray = array();
                         $rootArray = $extend::$methodName(0,$rootArray);
                          if(count($rootArray)>0)
                          {
                              $hasRoot =true;
                          }
                      }  
                    $id = $this->getId();
                    $mark ="cascadeTop";
                    $oriChooseValue = $chooseValue;
                    $alreadychoosed = "";
                    if($chooseValue==null||trim($chooseValue)=="")
                      { 
                         if(!$hasRoot)
                         {
                           $chooseValue = $mark;
                         }
                      }
                    if($chooseValue!=null&&trim($chooseValue)!="")
                    {   

                        $chooseValue =strval($chooseValue);
                        $datamsg = new DataMsg();
                        $datamsg->findBySql($db,$sql);
                        $temp = $datamsg->getKeyValueArray("id","parentid",true);
                        $tempid = array();
                        while(1==1)
                        {
                        	if($alreadychoosed!="")
                        	{
                        		$alreadychoosed.=",";
                        	}
                        	$alreadychoosed.=$chooseValue;
                            if($chooseValue==null||trim($chooseValue)==""||$chooseValue==$topMark)
                            {
                              
                                 break;
                            }
                            else
                            {  
                                $chooseValue = $temp[$chooseValue];
                            }
                        }  
                    }
                    $where = "trim(a.parentid) = '' OR a.parentid IS NULL";
                    if($topMark!=null&&$topMark!='')
                    {  
                        $where .=" OR a.parentid='".$topMark."' ";
                    }
                    
                    $parentid = "IF((".$where."),'".$mark."',a.parentid) parentid";
                    $valueid = $pre.$id;
                    $spanid ="cascadeselectspan_".$id;
                    $sqlid = $id."_sql";
                    $alreadychoosedid = $id."_alreadychoosed";
                    $alreadychoosedArray = null;
                       if($alreadychoosed!=null&&trim($alreadychoosed)!="")
                        {
                            
                            $alreadychoosedArray = Array($mark,);
                            $alreadychoosedArray = array_merge($alreadychoosedArray,array_reverse(explode(",",$alreadychoosed)));
                            $idStr = "";
                            foreach($alreadychoosedArray as $c)
                            {
                                if($idStr!=null&&trim($idStr)!="")
                                {
                                    $idStr .=",";
                                }
                                $idStr .="'".$c."'";
                            }
                            if($idStr!=null&&trim($idStr)!="")
                            {
                                $where .= "  OR a.parentid IN (".  $idStr .")";
                            }
                        }
                        else
                        {
                                $alreadychoosedArray = Array($mark,);
                        }
                     
                    $finalsql =" SELECT a.id,a.name,".$parentid." FROM (".$sql.") a WHERE ".$where;
                    $datamsg = new DataMsg();
                    $datamsg->findBySql($db,$finalsql);
                    $result = $datamsg->getKeyDataMap("parentid",true,true);
                    $tempi = 1;
                     if($chooseValue==null||trim($chooseValue)=="")
                     {
                        $tempi = 2;
                     }
                    if($hasRoot)
                    {
                        $tempi = 0;
                    }
                    
                    $html = '<script type="text/javascript">

                                  function _changecascadefor_'.$id.'(id,i,choosed)
                                  {

                                      var idstr = "";
                                    
                                      var value = document.getElementById("'.$id.'_alreadychoosed").value;
                
                                      var valarr = value.split(",");
                                   

                                      for(var j=0;j<(Number(i)+Number(1));j++)
                                      {
                                         var temp = valarr[j];
                                         if(idstr!="")
                                         {
                                             temp = "," + valarr[j];    
                                         }
                                         idstr = idstr + temp;    
                                      }
                                      
                                      if(choosed!="")
                                      {
                                        idstr = idstr + "," + choosed;
                                      }
                               
                                      document.getElementById("'.$id.'_alreadychoosed").value = idstr;
                                      
                                      if(choosed!="")
                                      {
                                        document.getElementById("'.$valueid.'").value = choosed; 
                                      }
                                      else if(i==Number('.$tempi.'))
                                      {
                                         document.getElementById("'.$valueid.'").value = choosed; 
                                      }
                                      else if(i>0)
                                      {
                                         if(i!=Number('.$tempi.'))
                                         {
                                            document.getElementById("'.$valueid.'").value = valarr[i]; 
                                        }
                                      }

                                      var dt = "'.$defaultText.'";
                                      var dv = "'.$defaultValue.'";
                                      var topmark = "'.$topMark.'";
                                      var extend = "'.$extend.'";
                                      var methodname = "'.$methodName.'";
									$.post("'.QuickFormConfig::$quickFormBasePath.'cascadeAjax.php",
                                      	{id:id,alreadychoosed:idstr,sql:$("#'.$sqlid.'").val(),topmark:topmark,defaulttext:dt,defaultvalue:dv,extend:extend,methodname:methodname},
                                      	function(result){
                                      	  $("#'.$spanid.'").html(result);
                                        });
                                  	}
                                </script>';
                    $startvalue = $defaultValue;
                    if($oriChooseValue!=null&&trim($oriChooseValue)!="")
                    {
                         $startvalue = $oriChooseValue;
                    }
                    $html .='<input id="'.$alreadychoosedid.'" name="'.$alreadychoosedid.'" type="hidden" value="'.implode(",",$alreadychoosedArray).'" />
                             <input id="'.$valueid.'"  name="'.$valueid.'" type="hidden" value="'.$startvalue.'" />
                             <input id="'.$sqlid.'"  name="'.$sqlid.'" type="hidden" value="'.$sql.'" />';

                    $html .= '<span id="'.$spanid.'">';
                      $k = 0;
                      if(!$hasRoot&&count($alreadychoosedArray)>1)
                      {
                         $k = 1;
                      }
                      for($i=$k;$i<count($alreadychoosedArray);$i++)
                      {         
                                if($hasRoot&&$i==0)
                                {
                                    
                                        $defaultV = $defaultValue;
                                        $hid ="cate_choose_choosed_".($i);
                                        $choose = new HtmlElement($hid,$hid);
                                        $choose->setParam("class","form-control");
                                        $js = "_changecascadefor_".$id."('".$id."','".$i."',this.options[this.selectedIndex].value)";
                                        $choose->setFunction("onChange",$js);
                                        $html .= $choose->getSelectByArray($rootArray,"id","name",$alreadychoosedArray[$i+1],$defaultText,$defaultValue)."<br>"; 
                                        continue;
                                }
                                
                                $key  =strval($alreadychoosedArray[$i]);
                                if($key==$topMark)
                                {
                                    $key = $mark;
                                }     
                                $sd = $result[$key];
                                if(count($sd)>0||$i==0)
                                {
                                    $hid ="cate_choose_choosed_".($i);
                                    $choose = new HtmlElement($hid,$hid);
                                    $js = "_changecascadefor_".$id."('".$id."','".$i."',this.options[this.selectedIndex].value)";
                                    $choose->setFunction("onChange",$js);
                                    $choose->setParam("class","form-control");
                                    if($extend!=null&&$methodName!=null&&trim($extend)!=""&&trim($methodName)!="")
                                    {                                      
                                       $tempsd =  $extend::$methodName($i,$sd);
                                       if(is_array($tempsd))
                                       {
                                            $sd = $tempsd;
                                       }
                                    }  
                                    
                                    $defaultV = $defaultValue;
                                    if($i>0)
                                    {
                                            $defaultV = $alreadychoosedArray[$i];        
                                    }       
                                    $choose->setParam("class","form-control");
                                    $html .= $choose->getSelectByArray($sd,"id","name",$alreadychoosedArray[$i+1],$defaultText,$defaultValue)."<br>"; 
                                }         
                      }
                      $html .= '</span>';
                    return $html;

              }

              public function getPanel($content,$title=null,$editTitle=false,$reload=false,$close=false,$class="panel-info")
              { 
                  if(!$close)
                  {
                     $this->setParam("data-close","false");
                  }
                  if(!$reload)
                  {
                     $this->setParam("data-reload","false");
                  }
                  if(!$editTitle)
                  {
                     $this->setParam("data-edit-title","false");
                  }
                  if($class==null&&trim($class)=="")
                  {
                    $class = "";
                  }
                  $class ="panel lobipanel ".trim($class);
                  if($this->param["class"]!=null&&trim($this->param["class"])!="")
                  {
                    $class =trim($this->param["class"])." ".$class;
                  }
                  $this->setParam("class",$class);
                  $result ='<div ';
                  $result.= $this->getParamString();
                  $result.= $this->getFunctionString();     
                  $result .=" />";   
                  $result .='<div class="panel-heading">';
                  if($title!=null&&trim($title)!="")
                  {
                     $result .='<div class="panel-title"<h4>'.$title.'</h4></div>';
                  } 
                  $result .='</div>';
                  $result .='<div class="panel-body">'.$content.' </div></div>';
                  return $result;

              }
    }

?>