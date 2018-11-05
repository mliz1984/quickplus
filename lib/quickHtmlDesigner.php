<?php 
   class QuickHtmlDesigner
   {
   		public function getCellAttr($cellContent,$col,$row)
   		{
   			$html .= ' title="' . $col . $row . '"';
			$html .='onmouseover="this.style.background=\'red\';"onmouseout="this.style.background=\'\'";';
			return $html;
   		}
   }

?>