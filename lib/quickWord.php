<?php
    require_once(dirname(__FILE__) . "/quickFormConfig.php");
    require_once(dirname(__FILE__) . "/commonTools.php");
	class QuickWord 
	{
		protected $file = null;
		function __construct($file=null)
		{
			$this->file = $file;
		}
		public function setFile($file)
		{
			$this->file = $file;
		}
		public function getFile($file)
		{
			return $this->file;
		}
		protected function processFile()
		{	
			$tmpPath = quickFormConfig::$tmpPath.time()."/";
			FileTools::createDir($tmpPath);
			$zip = new ZipArchive();
			if ($zip->open($this->file) === TRUE) {
				  $zip->extractTo($tmpPath);
				  $zip->close();
			}
			return $tmpPath;

		} 
		

	    public function getFontStyleArray($result,$fontStyle)
	    {
	    	
	       foreach($fontStyle as $rpr)
		   {
		 		switch($rpr["tag"])
		 		{
		 			case "w:sz":
		 			$result["size"] = intval(trim($rpr["attributes"]["w:val"])/2);

		 		    break;
		 			case "w:color":
		 		    $result["color"] = trim($rpr["attributes"]["w:val"]);
		 			break;
		 			case "w:b":
		 		    $result["bold"] = true; 
		 		    break;
		 			case "w:u":
		 			$result["underline"] = true; 
		 			break;
		 			case "w:i":
		 			$result["italic"] = true; 
		 			break;
				}

		 	} 
		 	return $result;
	    }

		public function getContentArray($filePath=null)
		{
			if($filePath=null||trim($filePath)=="")
			{
				$filePath = $this->processFile();
			}
			$xmlPath = $filePath. "/word/document.xml"; 
			$reader = new XMLReader();   
 			$reader->open($xmlPath);   
 			$xmlArray =XmlTools::xmlToAssoc($reader);
 			$result = Array();
 			foreach($xmlArray as $x)
 			{

 				if($x["tag"] == "w:document")
 				{
 					foreach($x["value"] as $d)
 					{
 						if($d["tag"]=="w:body")
 						{
		 					foreach($d["value"] as $b)
		 					{
		 						if($b["tag"]=="w:p")
		 						{	
		 							$debug = false;
		 							if($b["attributes"]["w:rsidRDefault"]=="002459B5"&&$b["attributes"]["w:rsidP"]=="002459B5")
		 							{
										$debug = true;
									}	
		 							$p_result = Array();
		 							foreach($b["value"] as $p)
		 							{
		 							  
		 							    if($p["tag"] == "w:pPr")
		 							    {
		 							    	foreach($p["value"] as $ppr)
		 							    	{

		 							    		if($ppr["tag"] == "w:rPr")
		 							    		{
		 							    			$p_result = $this->getFontStyleArray($p_result,$ppr["value"]);
		 							    		}
		 							    		else if($ppr["tag"] == "w:jc")
		 							    		{

											 			if(isset($ppr["attributes"]["w:val"])&&trim($ppr["attributes"]["w:val"])!="")
													 	{
													 		$p_result["align"] = trim($ppr["attributes"]["w:val"]);
													 	}
												}
		 							    	}
		 							    }
		 							    elseif($p["tag"] == "w:r")
		 							    {
		 							    	$r_result = Array();
		 							    	$r_result["bold"] = false; 
		 							    	$r_result["underline"] = false; 
		 							    	$r_result["italic"] = false; 
		 							    
		 							    	foreach($p["value"] as $r)
		 							    	{
		 							    		
		 							    		if($r["tag"]=="w:t")
		 							    		{
		 							    			$v = $r["value"];
		 							    			if(!isset($r["attributes"]["xml:space"])||$r["attributes"]["xml:space"]!="preserve")
		 							    			{
		 							    				$v  = trim($v);
		 							    			}

		 							    		    $r_result["text"] = $v;
		 							    		}
		 							    		elseif($r["tag"]=="w:rPr")
		 							    		{
		 							    			$r_result = $this->getFontStyleArray($r_result,$r["value"]);
		 							    			
		 							    		}
		 							    	}
		 							    	if(isset($r_result["text"])&&is_string($r_result["text"])&&trim($r_result["text"])!="")
		 							    	{
		 							    		$p_result["content"][] = $r_result;
		 							    	
		 							    	}
		 							    }
		 							    
		 							}
		 							$result[] = $p_result;
		 						}
		 					}
		 				}
	 				}
 				}
 			}
 			FileTools::unlinkDir($filePath);
 			return $result;
		}
	
    protected function getFontStyle($styleName,$p,$c)
    {
    	$style = $c[$styleName];
    	$have = false;
    	if($style!=null)
    	{
    		if(is_bool($style))
    		{
    			$have = true;
    		}
    		else if(trim($style)!="")
    		{
    			$have = true;
    		}
    	}
    	if(!$have)
    	{
    		$style = $p[$styleName];
    	}
        return $style;
    }

	public function getHtml()
	{
		$contentArray = $this->getContentArray();
		$result = "";
		foreach($contentArray as $p)
		{  
			if(is_array($p["content"])&&count($p["content"]) > 0)
			{
				$result .= "<p ";
				if(isset($p["align"])&&trim($p["align"])!="")
				{
					$result .= " align='".trim($p["align"]."'");
				}
				$result.=">";
				foreach($p["content"] as $content)
				{
					$size = false;
					$color = false;
					$sizeString = $this->getFontStyle("size",$p,$content);
					
					if(isset($sizeString)&&trim($sizeString)!="")
					{
						$size =true;
					}
					$colorString = $this->getFontStyle("color",$p,$content);
					if(isset($colorString)&&trim($colorString)!="")
					{
						$color =true;
					}
					if($size||$color)
					{
						$text="<font ";
				     	if($size)
				     	{
				     		$text .=" size='".$sizeString."' ";
				     	}
				     	if($color)
				     	{
				     		$text .=" color='#".$colorString."' ";
				     	}
				     	$text.=">";
				     	$text.=$content["text"];
					    $text.="</font>";	
					    $bold =  $this->getFontStyle("bold",$p,$content);
					    if($bold)
					    {
					    	$text = "<b>".$text."</b>";
					    }
					    $italic =  $this->getFontStyle("italic",$p,$content);
					    if($italic)
					    {
					    	$text = "<i>".$text."</i>";
					    }
					     $underline =  $this->getFontStyle("underline",$p,$content);
					     if($underline)
					    {
					    	$text = "<u>".$text."</u>";
					    }
					    $result .=$text;
					}
				}
				$result .="</p>";
			}
		}
		return $result;
	}
}
?>