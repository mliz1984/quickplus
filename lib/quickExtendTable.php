<?php  
   class QuickExtendTable
   {
   	    protected $extendTablePrefix = "";
   	    public function setExtendTablePrefix($extendTablePrefix)
   	    {
   	    	$this->extendTablePrefix = $extendTablePrefix;
   	    }
   	    public function getExtendTablePrefix()
   	    {
   	    	return $this->extendTablePrefix;
   	    }
   		protected $colData = Array();
   		public function setCol($colid,$title,$html,$addjs="")
   		{
   			$this->colData[$colid] = Array("title"=>$title,"html"=>$html,"addjs"=>$addjs);
   		}
   		public function getHtml($id,$mustHaveColNum=0,$initColNum=1,$fixDataList=null,$addText="Add",$deleteText="Delete",$hiddenColArray=null)
   		{
   			if($initColNum<$mustHaveColNum)
   			{
   				$initColNum = $mustHaveColNum;
   			}
   		    $html.='<table id="'.$id.'_table" class="display" cellspacing="0" width="100%"><thead><tr>';
   		    $js = "<script>var quickextendtable_".$id." = $('#".$id."_table').DataTable({paging: false,bLengthChange:false,bInfo:false,bFilter:false});";  		  
   		    $js.= "function _delExtendTable".$id."Row(obj){var rowNode  =  quickextendtable_".$id.".row($(obj).parents('tr')); quickextendtable_".$id.".row(rowNode).remove().draw( false );return false;}";
   		    $js.="$('#".$id."_addRow').on( 'click', function () {
                 var rowid = _randomString();
       					 var rowNode = quickextendtable_".$id.".row.add( [";
          $js2 =" var rowid = _randomString();var rowNode = quickextendtable_".$id.".row.add( [";
          $addjs = "";
   		    foreach($this->colData as $colid =>$colinfo)
   		    {
   		    	$html.="<th>".$colinfo["title"]."</th>";
   		    	$colHtml = $colinfo["html"];
   		    	$js.="'<div id=\'".$colid."_'+ rowid +'\' class=\''+ rowid +'\'>".str_replace("'","\'",$colHtml)."</div>',";
            $js2.="'<div id=\'".$colid."_'+ rowid +'\' class=\''+ rowid +'\'>".str_replace("'","\'",$colHtml)."</div>',";
            if($colinfo["addjs"]!=null&&trim($colinfo["addjs"]!=""))
            {
              $addjs.=$colinfo["addjs"];
            }
            
   		    }
   		    $html.='<th><button id="'.$id.'_addRow">'.$addText.'</button></th></tr></thead><tbody>';
   		    $hiddenHtml = "";
          if(is_array($hiddenColArray))
          {            
              foreach($hiddenArray as $tkey => $tvalue) 
              {
                $thtmlid = $this->getExtendTablePrefix().$extendTableId."[".$tkey."][]";
                $thidden = new HtmlElement($thtmlid,$thtmlid);
                $hiddenHtml.=$thidden->getHidden($tvalue);
              }
          }
          $js.=$hiddenHtml."'<button class=\"btn btn-primary btn-sm\" onClick=\"_delExtendTable".$id."Row(this)\">".$deleteText."</button>']).draw().node();".$addjs."return false});";
   		    $js2.="'']).draw().node();".$addjs;
   		    $html.="</table>";
 
             if(is_array($fixDataList))
             {
               $fixDataCount = count($fixDataList);
               if($mustHaveColNum<$fixDataCount)
               {
                  $mustHaveColNum = $fixDataCount;
               }
             }
             for($i=0;$i<$mustHaveColNum;$i++)
             {
                if(isset($fixDataList[$i]))
                {
                  $js.=$fixDataList[$i];
                }
                else
                {
                  $js.=$js2;
                } 
             }
   		    $leftNum = $initColNum - $mustHaveColNum;
             for($i=0;$i<$leftNum;$i++)
   		    {
   		    	$js.="$('#".$id."_addRow').click();";	
   		    }
   		    $js.="</script>";
   		    $html.=$js;
   		    return $html;
   		}
   }
?>