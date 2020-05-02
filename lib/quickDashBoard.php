<?php 
namespace Quickplus\Lib;
  class quickDashBoard extends quickForm
  {
     protected $dataSource = Array();
     protected $dbnameValue = Array();
     protected $data = Array();
     protected $dataSourceLink = Array();
     protected $dataParts = Array();
     protected $dataSourceProcessMethod = Array();
     protected $comLink = Array();
     protected $autoRefresh = false;
     protected $customCol = false; 
     protected $defaultDashboardId = "dashboard";
     public function setDefaultDashboardId($defaultDashboardId)
     {
        $this->defaultDashboardId = $defaultDashboardId;
     }
     public function setComLink($dashboardid,$id,$oriid)
     {
        $this->comLink[$dashboardid][$id] = $oriid;
     }  

     public function setDataSourceProcessMethod($dashboardid,$sourceid,$method,$link=false)
     {
        $this->dataSourceProcessMethod[$dashboardid][$sourceid]=Array("method"=>$method,"link"=>$link);
     }
     public function regDataSource($dashboardid,$sourceid,$classname,$path=null)
     {
         $classFound = true;
         if(!class_exists($classname)&&!empty($path)) 
         {  
            $classFound = false;
            require_once($path);
            if(class_exists($classname))
            {
              $classFound = true;
            }
         }
         if($classFound)
         {
            $this->dataSource[$dashboardid][$sourceid] = $classname;
         }
     }
     public function setDataSource($dashboardid,$id,$sourceid)
     {
        $this->dataSourceLink[$dashboardid][$id] = $sourceid;
     }

     public function setDbnameValue($dashboardid,$sourceid,$dbname,$value,$link=false)
     {
         $this->dbnameValue[$dashboardid][$sourceid][$dbname]= Array("value"=>$value,"link"=>$link);
     }

     protected function getSearchArray($dashboardid,$sourceid,$src)
     {
         $ret = $src;
         foreach($this->dbnameValue[$dashboardid][$sourceid] as $dbname=>$data)
         {
            $value = $data["value"];
            $link = $data["link"];
            if($link)
            {
              $ret[$this->getSearchPrefix().$dbname] = $ret[$this->getSearchPrefix().$value];
            }
            else
            {
              $ret[$this->getSearchPrefix().$dbname] = $value;
            }
         }
         return $ret;

     }
     protected function getQucikFromForDashboard($dashboardid,$sourceid)
     {
           $quickFormClass = $this->dataSource[$dashboardid][$sourceid];
           $ret = new  $quickFormClass();
           $ret->setBlank($this->isBlank());
           return $ret;
          
     }


     protected function getDashboardResult($dashboardid,$sourceid,$quickForm,$src)
     {
          $ret = Array();
          if(isset($this->data[$dashboardid][$sourceid])&&is_array($this->data[$dashboardid][$sourceid]))
          { 
            $ret = $this->data[$dashboardid][$sourceid];
          }   
          else
          {
            $quickForm->setSearchPrefix($this->getSearchPrefix());
            $quickFormSrc = $this->getSearchArray($dashboardid,$sourceid,$src);
            
            $quickFormDrawer = new quickFormDrawer();
            $quickFormDrawer->setLoadRes(false);
            $quickForm = $quickFormDrawer->setQuickForm($this->getDb(),$quickForm);
            $quickFormDrawer->setLoadTotalInfo(false);
            $obj = $quickFormDrawer->getForm($this->getDb(),$quickFormSrc,1,0,false,false);
            $ret = $obj->getResult();
            $this->data[$dashboardid][$sourceid] = $ret;
          }

        return $ret;
     }

      

     public function getDashboardResultParts($dashboardid,$sourceid,$quickform,$data,$src)
     {  
         $ret = Array();
        
          if(is_array($this->dataParts[$dashboardid][$sourceid]))
          {
             $ret = $this->dataParts[$dashboardid][$sourceid];
          }
          else 
          {
             $method =  $this->dataSourceProcessMethod[$dashboardid][$sourceid]["method"];
             $link =  $this->dataSourceProcessMethod[$dashboardid][$sourceid]["link"];
             if(!empty($method))
             {
                if($link)
                {
                    $ret = $quickform->$method($dashboardid,$result,$src);
                }
                else
                {
                    $ret = $this->$method($dashboardid,$result,$src);
                }
                $this->dataParts[$dashboardid][$sourceid] = $ret;
            }
          }
        return $ret;
     }
     public function getDashboardHtml($src,$dashboardid=null)
        { 

            $haveChart = false;
            if(empty($dashboardid))
            {
                $dashboardid = $this->defaultDashboardId;
            }
            $ret = "";
            if(!$this->isBlank())
            {
                $src["searchSign"] = 1; 
            }
            if(is_array($this->dashboardGroup[$dashboardid]["content"]))
            {

                $array = $this->dashboardGroup[$dashboardid]["content"];
                $j  = count($array);
                $max_row_in_dashboard = $this->getMaxRowInDashboard();
                if($max_row_in_dashboard>0&&$j>$max_row_in_dashboard)
                {
                    $j = $max_row_in_dashboard;
                }
                foreach($array as $rowid=>$arr)
                {
                    $i = count($arr);

                    foreach($arr as $colid=>$data)
                    {
                        $type =$data["type"];
                        $id = $data["id"];
                        $oriid = $id;

                        $groupid = $data["groupid"];
                        $width = $data["width"];
                        $height = $data["height"];
                        $dataKey = $data["dataKey"];
                        $sourceid = $this->dataSourceLink[$dashboardid][$id];
                         $oriid = $id;
                        
            
                        $quickForm = $this->getQucikFromForDashboard($dashboardid,$sourceid);
                      
                       
                        $result = $this->getDashboardResult($dashboardid,$sourceid,$quickForm,$src);
                        
                        $resultParts = $this->getDashboardResultParts($dashboardid,$sourceid,$quickForm,$result,$src);
                        $rowData = $result;
                        $form = $this;
                        $quickFormDrawer = new quickFormDrawer();
                        $quickFormDrawer->setLoadRes(false);
                        if(isset($this->comLink[$dashboardid][$id]))
                        {
                          $oriid = $this->comLink[$dashboardid][$id];
                          $form = $quickFormDrawer->setQuickForm($this->getDb(),$quickForm);
                        }
                        else
                        {
                           $form = $quickFormDrawer->setQuickForm($this->getDb(),$form); 
                        }
                        $form->setResult($result);

                        $key ="";
                        if(!empty($dataKey)&&is_array($resultParts[$dataKey]))
                        {
                            $rowData = $resultParts[$dataKey];
                            $key = $dataKey;
                        }
                        $quickHtmlDrawer = new QuickHtmlDrawer($id);
                         $quickHtmlDrawer->setPanelName("");
                  
                        
                        if($type=="chart")
                        {
                            $form->setChartWidth($id,"95%");    
                            if(empty($height))
                            {
                                $height = intval(rtrim($this->getChartHeight($id),"%")/$j);     
                            }
                            $form->setChartHeight($id,$height."%");
                            $html = $quickHtmlDrawer->getChartHtml($form,$oriid,$rowData,$src);
                             $haveChart = true;
                        }
                        else if($type=="datatable")
                        {
                            $pageRows = $this->getDataTablePageRows($dashboardid,$id);
                            $quickHtmlDrawer->setParameter("dashboardid",$dashboardid);
                            $datatableid = $id;
                            $quickHtmlDrawer->setParameter("datatableid",$datatableid);
                            $quickHtmlDrawer->setParameter("dataKey",$key); 
                            $id = StringTools::getRandStr();
                            $array = CommonTools::getDataArray($src,$this->getSearchPrefix());
                            
                            foreach($array as $k =>$v)
                            {
                                if(is_array($v))
                                {
                                    $temp = "";
                                    foreach($v as $vk => $vv)
                                    {
                                        if(!empty($vv))
                                        {
                                            $temp.=','.$vv;
                                        }
                                    }
                                    $temp = ltrim($temp,",");
                                    $v = $temp;
                                }
                                
                                $quickHtmlDrawer->setParameter($this->getSearchPrefix().$k,$v);
                                
                            }

                            $obj = $from;
                            $obj->setSearchPrefix($this->getSearchPrefix());
                            $obj->setPageRows($pageRows);
                            $ret = $this->loadDataTableColSetting($dashboardid,$datatableid);
                            
                            $html = $quickHtmlDrawer->getDataTableHtml($obj,$ret);

                        }
                        else if($type=="simplecard"||$type=="datacard")
                        {
                        
                            $title = $data["title"];
                            $content = $data["content"];
                            if($type=="datacard")
                            {
                                $content = $resultParts[$content];
                                $method = $data["method"];
                                if(!empty($method))
                                {
                                    $content = $this->$method($dashboardid,$id,$content);
                                }
                            }
                            $html = $quickHtmlDrawer->getQuickCardHtml($oriid,$title,$content);
                        
                        }
                        else
                        {
                            $html = $quickHtmlDrawer->getStatisticHtml($form,$oriid,$rowData,$src);
                        }
                        $this->setColByHtml($rowid,$colid,$html, $groupid,$width);
                    }
                }
                $ret = $this->getHtml();
            }
            if($haveChart)
            {
                 $ret.=$this->getChartAutoSizeJs();
            }
            return $ret;
        }
        protected function getChartAutoSizeJs()
        {
            $js  ='<script type="text/javascript">
                    $(\'.lobipanel\').on(\'onSmallSize.lobiPanel\', function(ev, lobiPanel){
                          var event = new Event(\'resize\');
                           window.dispatchEvent(event);
                        });
                    $(\'.lobipanel\').on(\'onFullScreen.lobiPanel\', function(ev, lobiPanel){
                       var event = new Event(\'resize\');
                           window.dispatchEvent(event);
                        });
                    $(\'.lobipanel\').on(\'resizeStop.lobiPanel\', function(ev, lobiPanel){
                        var event = new Event(\'resize\');
                           window.dispatchEvent(event);
                        });
                    $(\'.lobipanel\').on(\'onPin.lobiPanel\', function(ev, lobiPanel){
                        var event = new Event(\'resize\');
                           window.dispatchEvent(event);
                        });
                  $(\'.lobipanel\').on(\'onUnpin.lobiPanel\', function(ev, lobiPanel){
                        var event = new Event(\'resize\');
                           window.dispatchEvent(event);
                        });
                   </script>';
            return $js;
        }
  }