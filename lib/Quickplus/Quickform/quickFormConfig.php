<?php
namespace Lib\Quickplus\Quickform;

    const defaultTableWidth = "1500px";
    const defatltFullScreenMode = false;
	const defaultTableAllowWrap = false;
	const defaultSearchDivMode = true;
	const defaultTemplatePath = "/template";

   class QuickFormConfig
   {
		public  static $defaultChartRenderer = "canvas";
		public  static $quickFormBasePath = "/lib/";
		public  static $quickFormClassPath = "/class/";
		public  static $quickFormPagePath = "/pages/";
	 	public  static $quickFormResourcePath = "/js/quickform/";
	 	public  static $ueditPath = "/js/ueditor/";
	 	public  static $datePickerPath = "/js/datepicker/";
	 	public  static $jquery = "/js/jquery-1.11.1.min.js";
	 	public  static $jqueryUiPath = "/js/jqueryui/";
	 	public  static $jqueryValidationPath = "/js/jquery-validation/";
	 	public  static $ingridPath = "/js/ingrid/";
	 	public  static $qnuiPath = "/js/qnui/";
	 	public  static $quickFormMethodPath = "/lib/quickform/";
	 	public  static $encode = "utf-8";
	 	public  static $dbEncode = null;
	 	public  static $SqlType = "\Lib\DbModule\Database";
	 	public  static $tmpPath = "/tmp";
	 	public  static $quickWordFilePath = "/public/QuickWordFiles/";
	 	public  static $dirSep = "\\";
	 	public  static $pathSep = ";";
	 	public  static $imagePickerPath = "/js/image-picker/"; 
	 	public  static $chosenPath = "/js/chosen/";
	 	public  static $defaultExportFormat = "CSV";
	    public  static $defaultChartWidth ="1000px";
	    public  static $defaultChartHeight ="800px";
	    public  static $twilioPath = "/lib/twilio/";
	    public  static $quickTemplate = true;
	    public  static $htmlExtend = "/lib/htmlExtend";
	    public  static $quickLoginManagerSrc = "/include/loginManager.php";
	    public  static $quickLoginManagerClassname = "LoginManager";
	    public  static $quickFormRightControl = true;
	    public  static $quickPdfRenderer = "tcPDF";
 		public  static $quickPdfRendererPath  ="/lib/tcpdf";
		public  static $customCol = true;
 		public  static $customColText = "Column Visibility";
 		public  static $autoRefreshTimeText = "Auto Refresh Time";
 	    public  static $menuIdMark = "menuid";
 	    public  static $subFormMark = "subForm";
 		public  static $viewList = Array("/view/standardView.php"=>"Standard View");
   }   

?>