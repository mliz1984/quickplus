<?php

require_once($_SERVER['DOCUMENT_ROOT']."/lib/parameters.php");
require_once($_SERVER['DOCUMENT_ROOT']."/lib/dbmodule.php"); 
require_once($_SERVER['DOCUMENT_ROOT']."/lib/quickFormDrawer.php"); 
require_once($_SERVER['DOCUMENT_ROOT']."/lib/quickFormConfig.php");
require_once($_SERVER['DOCUMENT_ROOT']."/lib/quickMenu.php");
require_once($_SERVER['DOCUMENT_ROOT']."/class/session.php");
require_once($_SERVER['DOCUMENT_ROOT']."/lib/quickLoginManager.php");          
$db = new  QuickFormConfig::$SqlType();
$loginmanager = QuickLoginManager::getQuickLoginManager();
$flag = intval(ArrayTools::getValueFromArray($_REQUEST,"flag"));
if($flag==1)
{
	$loginmanager->reloadUserRight($db);
}
$menu = $loginmanager->getMenu();


?>
<!DOCTYPE html>
<html>

<head>


<meta http-equiv="Content-Type" content="text/html;charset=<?php echo QuickFormConfig::$encode?>" >
<script type="text/javascript" src="/js/jquery-1.11.1.min.js"></script>
<link type="text/css" href="/js/quickform/bootstrap/css/bootstrap.min.css" rel="stylesheet" media="all" />
<script type="text/javascript" src="/js/quickform/bootstrap/js/bootstrap.min.js"></script>
<script type="text/javascript" src="/js/quickform/jquery.session.js"></script>
</head>
<body>
<!--
<div class="col-md-2">
</div>
<div class="col-md-2">
  <br><br>
   <span class="glyphicon glyphicon-th-list"></span>²Ëµ¥<br>
  --<a href='subpages/listreports.php'>±¨±í</a><br><br>
   <span class="glyphicon glyphicon-th-list"></span>¹ÜÀí<br>
  --<a href='subpages/pageregister.php'>×¢²áÒ³Ãæ</a><br>
  --<a href='subpages/addadmin.php'>Ìí¼Ó¹ÜÀíÔ±</a><br>
  --<a href='subpages/logout.php'>×¢Ïú</a><br>

</div>
<div class="col-md-8">
</div>-->


<?php
  /*
	$sql = "SELECT a.id,a.name,a.showname,a.order_sequence,a.parentpage_id,a.url FROM [dbo].[qp_menu_manage] a WHERE a.parentpage_id=0 ORDER BY a.order_sequence;";  // Load page settings from database

	$db = new  QuickFormConfig::$SqlType();
	$datamsg = new DataMsg();
	$datamsg -> findBySql($db,$sql);
	$totalrows = $datamsg->getSize(); // return the total rows of the results
	$menu = new QuickMenu();
	for($i=0;$i<$totalrows;$i++)
	{
		$data = $datamsg->getData($i);
		$id = $data->getString("id");
		$parentpage_id = $data->getString("parentpage_id");
		$showname = $data->getString('showname');
		if($parentpage_id==0)
		{
			
			//echo "@";
			//echo "pageid:".$id." parent:".$parentpage_id." showname:".$showname."<br>";
			//$menu->addData($id,$parentpage_id,$showname);
			
			$datamsg_subpage = new DataMsg();
			$sql_subpage = "SELECT a.id,a.name,a.showname,a.order_sequence,a.parentpage_id,a.url,a.target FROM [dbo].[qp_menu_manage] a WHERE a.parentpage_id=".$id." ORDER BY a.order_sequence;";
			$datamsg_subpage -> findBySql($db,$sql_subpage);
			$totalrows_subpage = $datamsg_subpage->getSize(); 
			if($totalrows_subpage>0) // has subpages
			{
				//echo 1111;
				$menu->addCategory($id,$parentpage_id,$showname);
				for($ii=0;$ii<$totalrows_subpage;$ii++)
				{
					$data_subpage = $datamsg_subpage->getData($ii);
					$id_subpage = $data_subpage->getString("id");
					$parentpage_id_subpage = $data_subpage->getString("parentpage_id");
					$showname_subpage = $data_subpage->getString('showname');
					$url = $data_subpage->getString('url');
					$target = $data_subpage->getString('target');
					//echo "pageid:".$id_subpage." parent:".$parentpage_id_subpage." showname:".$showname_subpage."<br>";

					$menu->addData($id_subpage,$parentpage_id_subpage,$showname_subpage,$url,$target);

				}
			}
		}
	}

*/
	echo $menu->getHtml('menu');

?>

  </body>

 </html>

