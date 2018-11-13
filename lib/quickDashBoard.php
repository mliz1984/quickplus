<?php 
namespace Quickplus\Lib;
use Quickplus\Lib\quickForm;
use Quickplus\Lib\quickFormDrawer;
  class QuickDashBoardBase extends quickForm
  {
      protected $commonSettingPrefix ="csp_";
      protected $panelSettingPrefix="psp_";
      protected $widgetSettingPrefix = "wsp_";

          public function setWidgetSettingPrefix($widgetSettingPrefix)
          {
             $this->widgetSettingPrefix = $widgetSettingPrefix;
          }
          public function getWidgetSettingPrefix()
          {
             return $this->widgetSettingPrefix;
          }

          public function setCommonSettingPrefix($commonSettingPrefix)
          {
              $this->commonSettingPrefix = $commonSettingPrefix;
          }
          public function setPanelSettingPrefix($panelSettingPrefix)
          {
             $this->panelSettingPrefix = $panelSettingPrefix;
          }
          public function getPanelSettingPrefix()
          {
             return $this->panelSettingPrefix;
          }
  }
	class QuickDashBoard extends QuickDashBoardBase
	{
	    protected $commonSetting = Array();
	    protected $widgetList = Array();
	    protected $panelSetting = null;
          public function getCommonSettingPrefix()
          {
             return $this->commonSettingPrefix;
          }
	    public function setCommonSetting($key,$value)
	    {
	    	$this->commonSetting[$key] = $value;
	    }

	    public function getCommonSetting($key)
	    {
	    	return $this->commonSetting[$key];
	    }

      	public function registWidget($widgetId,$widgetName,$widgetClassName,$widgetClassSrc,$params=Array())
      	{
      		$this->widgetList[$widgetId] = Array("widgetId"=>$widgetId,"widgetName"=>$widgetName,"widgetClassName"=>$widgetClassName,"widgetClassSrc"=>$widgetClassSrc,"widgetParams"=>$params);
      	}
        public function registQuickForm($widgetId,$widgetName,$quickFormClassName,$quickFormClassSrc,$params=Array())
        {
           $params["quickFormClassName"] = $quickFormClassName;
           $params["quickFormClassSrc"] = $quickFormClassSrc;
           $widgetClassName = "QuickFormWidget";
           $widgetClassSrc = dirname(__FILE__)."/quickWidget.php";
           $this->registWidget($widgetId,$widgetName,$widgetClassName,$widgetClassSrc,$params);
           
        }
        public function loadSrc($widgetid,$src)
        {
           $obj = $this->getWidgetObjById($widgetid);
           return $obj->loadSrc($src);
        }
        public function saveSrc($params,$src)
        { 
           $obj = $this->getWidgetObj($params);
           return $obj->saveSrc($src);
        }
        public function getWidgetObj($src)
        {  
            $db = $this->getDb();
            $widgetid = $src["widgetid"];
            $position = $src["position"];
            $widgetConfig = $this->loadWidgetConfig($db,$src);
            $widgetConfig = $widgetConfig[$position];
            $obj = $this->getWidgetObjById($widgetid);
            $obj->setWidgetConfig($widgetConfig);
            return $obj;
        }
        public function getWidgetObjById($widgetid)
        {
            $db = $this->getDb();
            $widgetInfo = $this->widgetList[$widgetid];
            $widgetClassName = $widgetInfo["widgetClassName"];
            $widgetClassSrc = $widgetInfo["widgetClassSrc"];
            $widgetParams = $widgetInfo["widgetParams"];
            include_once($widgetClassSrc);
            $obj = new $widgetClassName();
            $obj->setWidgetPrefix($this->getWidgetSettingPrefix());
            $obj->setWidgetParams($widgetParams);
            $obj->setDb($db);
            return $obj;
        }
        public function getWidgetSettingHtml($src)
        {
            $obj = $this->getWidgetObj($src);
            return $obj->getWidgetConfigHtml($src);
        }

        public function getWidgetHtml($src)
        {
            $obj = $this->getWidgetObj($src);
            return $obj->showWidget($src);
        }
      	public function initWidgetCommon($db,$src)
      	{

      	}

      	public function initWidget($db,$src)
      	{

      	}
        public function initEdit($src = null) 
        {
           $this->setMethod("saveCommonSetting", "saveCommonSettingCommon", true);
           $this->setMethod("savePanelSetting", "savePanelSettingCommon", true);
        }

      	public function preLoad($db,$src=null)
      	{
           
      		$this->initWidgetCommon($db,$src);
      		$this->initWidget($db,$src);
      		$widgetlist = Array();
      		foreach($this->widgetList as $id => $widgetInfo)
      		{
      			$name = $widgetInfo["widgetName"];
      			$widget = Array();
      			$widget["attachdata_id"] = $id;
      			$widget["attachdata_name"] = $name;
      			$widgetList[] = $widget;
      		}
      		$this->addAttachDataByArray("WidgetList",$widgetlist);
      		if($src["module"]=="lunchCommonSetting")
      		{
      			$this->setInitLayoutMethod("initLayoutCommonSetting");
      		}
          elseif($src["module"]=="lunchPanelSetting")
          {
            $this->setInitLayoutMethod("initLayoutPanelSetting");
          }
      	}

        public function getPanelSetting($db,$src,$reload=false)
        {
          if($this->panelSetting == null||$reload)
          {
            $this->panelSetting = $this->loadPanelInfo($db,$src);
          }
          return $this->panelSetting;
        }

        public function initLayoutPanelSetting($src=null)
        {
          $db = $this->getDb();
          $panelSetting = $this->getPanelSetting($db,$src);
          $panelConfig = $this->loadWidgetConfig($db,$src);
          $panelClassSelectArray = Array(
                                         "Blue"=>"panel-info",
                                         "Orange"=>"panel-warning",
                                         "Red"=>"panel-danger",
                                         "Green"=>"panel-success",
                                         ); 
          $this->addAttachDataByMap("PanelClass",$panelClassSelectArray,true);
          $this->addAttachData("Widget",$this->widgetList,"widgetId","widgetName");
          
          $posmark = $src["position"];
          $setting = $panelSetting[$posmark];
          $config = $panelConfig[$posmark];
          $js = 'if($.trim($(this).val())==\'\'){$(\'#div_col_widgetsetting\').html(\'\');}else{$.post(\''.QuickFormConfig::$quickFormMethodPath.'getAjaxHtml.php\',{ formmark: \''.$this->getFormMark().'\', isreport: \''.$this->isReport().'\', method:\'getWidgetSettingHtml\',widgetid:$.trim($(this).val()),position:$(\'#'.$this->getPanelSettingPrefix().'position\').val()}, function(data) {$(\'#div_col_widgetsetting\').html(data);});};';
          $this->setCommonSelectOption("widgettype","onChange",$js );
          $this->addHidden($this->getPanelSettingPrefix()."position",$src["position"]);
          $this->setColByHtml("name","name","Panel Name:".$this->getCustomHtmlElement("panelname","defaultSearchShowMode",$setting["panelname"],$this->getPanelSettingPrefix()));
          $this->setColByHtml("Color","color","Panel Color:".$this->getCustomHtmlElement("panelclass","getSelectByPanelClass",$setting["panelclass"],$this->getPanelSettingPrefix()));
          $this->setColByHtml("Widget","widget","Widget:".$this->getCustomHtmlElement("widgettype","getSelectByWidgetWithPlease Choose One Widget...",$setting["widgettype"],$this->getPanelSettingPrefix()));
          $html = "";
          if($setting["widgettype"]!=null&&trim($setting["widgettype"])!="")
          {
              $arr = Array("widgetid"=>trim($setting["widgettype"]),"position"=>$posmark);
              $html = $this->getWidgetSettingHtml($arr);
          }  
          $this->setColByHtml("WidgetSetting","widgetsetting",$html);
          $this->setColByHtml("button", "Save", $this ->getSubmitButton("Save", "savePanelSetting"));
          
        }

      	public function initLayoutCommonSetting($src=null)
      	{
      		$db = $this->getDb();
          $setting = $this->loadCommonSetting($db,$src);
          $this->setColByHtml("nop","nop","Number Of Panel:".$this->getCustomHtmlElement("panelcount","defaultSearchShowMode",$setting["panelcount"],$this->getCommonSettingPrefix()));
          $this->setColByHtml("npr","npr","Number Per Row:".$this->getCustomHtmlElement("numberperrow","defaultSearchShowMode",$setting["numberperrow"],$this->getCommonSettingPrefix()));
          $this->setColByHtml("button", "Save", $this -> getSubmitButton("Save", "saveCommonSetting"));
          
      	}



        protected function getSettingValue($setting,$key,$defaultValue)
        {
          if(is_array($setting)&&$setting[$key]!=null&&trim($setting[$key])!="")
          {
              $defaultValue = $setting[$key];
          }
          return $defaultValue;
        }

        public function initLayout($src=null)
        {

            $db = $this->getDb();
            $this->addHidden("module");
            $this->addHidden("position");
            $commonSetting = $this->loadCommonSetting($db,$src);
            $panelSetting = $this->getPanelSetting($db,$src);
            $panelConfig = $this->loadWidgetConfig($db,$src);
            $panelcount = intval($commonSetting["panelcount"]);
            $numberperrow = intval($commonSetting["numberperrow"]);
            for($i=1;$i<=$panelcount;$i++)
            {
               $row = ceil($i/$numberperrow);
               $rowid = "row_".$row;
               $colid = "col_".$i;
               $positionid = "pos_".$i;
               $pcbid = "pcb_".$positionid;
               $setting = $panelSetting[$positionid];

               $panelConfig = new HtmlElement($pcbid,$pcbid);
               $js = "document.getElementById('module').value='lunchPanelSetting';document.getElementById('position').value='".$positionid."';".$this->getEditFormId().".submit();";
               $panelConfig->setFunction("onClick",$js);  
               $panelid = "panel_".$positionid;
               $panel = new HtmlElement($panelid,$panelid);
               $class =  $this->getSettingValue($setting,"panelclass","panel-info");
               $title = $this->getSettingValue($setting,"panelname","Panel ".$i);
               $html = $panelConfig->getUrl($title,"#");
               $content = "";
               $setting = $panelSetting[$positionid];

               if($setting["widgettype"]!=null&&trim($setting["widgettype"])!="")
               {
                  $arr = Array("widgetid"=>trim($setting["widgettype"]),"position"=>$positionid);
                  $content = $this->getWidgetHtml($arr);
               }  
               $this->setColByHtml($rowid,$colid,$panel->getPanel($content,$html,false,false,false,$class));

            }

            $js = "document.getElementById('module').value='lunchCommonSetting';document.".$this->getEditFormId().".submit();";
            $configButton = new HtmlElement("configBtn","configBtn");
            $configButton->setFunction("onClick",$js);  
            $this->setColByHtml("configButton","configButton",$configButton->getButton("DashBoard Config"));

        }
        public function saveCommonSetting($db,$src=null)
        {
            return false;
        }

        public function loadCommonSetting($db,$src=null)
        {

        }

        public function savePanelSettingCommon($db,$src=null)
        {
            $result = $this->savePanelSetting($db,$src);
            $this -> setMethodSuccess('savePanelSetting', 'Save Successfuly',UrlTools::getFullUrl());
            return $result;
        }

        public function saveCommonSettingCommon($db,$src=null)
        {
            $result = $this->saveCommonSetting($db,$src);
            $this -> setMethodSuccess('saveCommonSetting', 'Save Successfuly',UrlTools::getFullUrl());
            return $result;
        }

        public function loadPanelInfo($db,$src=null)
        {

        }
        
        public function loadWidgetConfig($db,$src=null)
        {

        }

      	public function getWidgetObject($id,$src)
      	{
      		$result = null;
      		if(is_array($this->widgetList[$id]))
      		{
      		    $widgetInfo = $this->widgetList[$id];
      		    $className = $widgetInfo["widgetClassName"];
      		    $classSrc = $widgetInfo["widgetClassSrc"];
      		    $params = $widgetInfo["widgetParams"]; 
      		    if($classSrc!=null&&trim($classSrc)!=""&&$className!=null&&trim($className)!="")
      		    {
      		    	include_once($classSrc);
      		    	$result = new $className($params,$src);
      		    }
      		}
      		return $result;
      	}

	}
?>