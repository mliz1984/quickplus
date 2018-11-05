<?php
   require_once(dirname(__FILE__)."/htmlelement.php");
   class QuickHtmlDesignerExtend
   {
         protected $designMode = true;
         public function setDesignMode($designMode)
         {
            $this->designMode = $designMode;
         }
   		public function getCellAttr($cellContent,$col,$row)
   		{
   			$html .= '  colval="'.strtolower($col).'" rowval="'.strtolower($row).'" title="' . $col . $row .'" linkcell=""'. ' id="cell_'.strtolower($col.$row).'"';
			
         if($this->designMode)
         {
            $html .=' onmouseover="this.style.background=\'red\';" onmouseout="this.style.background=\'\'"; ';
			   $html .=' onClick="template_editor.setContent(document.getElementById(\'cell_hidden_'.strtolower($col.$row).'\').value);document.getElementById(\'position\').innerHTML=\''.$col.$row.'\';document.getElementById(\'positionColValue\').value=\''.strtolower($col).'\';document.getElementById(\'positionRowValue\').value=\''.strtolower($row).'\';"';
         }
			return $html;
   		}

         public function getCellData($cellContent,$col,$row)
         {
          
            return $cellContent;;
         }

         public function getAfterCell($cellContent,$col,$row)
         {
            
            $id="cell_hidden_".strtolower($col.$row);
            $hidden = new HtmlElement($id,$id);
            $hidden->setParam("colval",$col);
            $hidden->setParam("rowval",$row);
            return $hidden->getHidden();
         }

         



   }

?>