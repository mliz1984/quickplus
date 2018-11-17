<?php
require($_SERVER['DOCUMENT_ROOT']."/vendor/autoload.php");
require_once($_SERVER['DOCUMENT_ROOT'] . "/class/session.php");
use Quickplus\Lib\parameters;
use Quickplus\Lib\DataMsg;
use Quickplus\Lib\QuickFormConfig;
use Quickplus\Lib\QuickLoginManager;

$db = new  QuickFormConfig::$SqlType();
$loginmanager = QuickLoginManager::getQuickLoginManager();
$result = $loginmanager->checkLogin();
$adminid = $loginmanager->getAccountID();
$lasturlsession = $loginmanager->getLastUrlSession();
 //echo $result;

/*
$session = new Session();
session_start();
//echo $_SESSION['id']."@@@";
if(!$session->validateuser())
{
  echo "<script>alert('ÎÞÈ¨ÏÞ²é¿´´ËÒ³')</script>";
  echo "<script>window.location='/index.php'</script>";
}
*/

?>

<!DOCTYPE html>
<html>

<head>


<meta http-equiv="Content-Type" content="text/html;charset=<?php echo QuickFormConfig::$encode?>" >
<script type="text/javascript" src="/js/jquery-1.11.1.min.js"></script>
<link type="text/css" href="/js/quickform/bootstrap/css/bootstrap.min.css" rel="stylesheet" media="all" />
<script type="text/javascript" src="/js/quickform/bootstrap/js/bootstrap.min.js"></script>
<script type="text/javascript" src="/js/quickform/jquery.session.js"></script>
<script>
$(document).ready(function(){
		var src="subpages/dashboard.php";
		 var lasturl =  $.session.get('<?php echo $lasturlsession?>');
		 if(lasturl!=null&&$.trim(lasturl)!="")
		 {
		 	src = lasturl;
		 	
		 }
		 $('#right').attr("src",src);
			});
 
</script>

</head>
<?php
 if(!$result)
 {
   echo "<script>alert('Please Login at first')</script>";
   echo "<script>window.location='/index.php'</script>";
 }
 //echo $result['id'];
?>

<FRAMESET rows='10%,*' frameborder="0"  >
  <frame style=""  src='subpages/header.php' overflow='hidden' scrolling=no>


  <FRAMESET cols='200,*' frameborder="0" >
    <frame name="left" src="subpages/menu.php" marginwidth="10" marginheight="10" > 
    <frame id="right" name="right"  style="" > 

  </FRAMESET>
</FRAMESET>



</html>
