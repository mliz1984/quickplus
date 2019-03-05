<?php
require_once($_SERVER['DOCUMENT_ROOT'] ."/vendor/autoload.php");
require_once($_SERVER['DOCUMENT_ROOT'] . "/class/session.php");
// Start the session
use Quickplus\Lib\parameters;
use Quickplus\Lib\QuickFormConfig;
use Quickplus\Lib\QuickLoginManager;
$db = new  QuickFormConfig::$SqlType();
if(!empty($_REQUEST['username']))
{
  $loginmanager = QuickLoginManager::getQuickLoginManager();
  $result = $loginmanager->login($db,$_REQUEST['username'],$_REQUEST['pwd']);
  $groupid=$loginmanager->getGroupID();
  //echo $groupid;
  //exit();
  if($result)
  {
     
     echo "<script>window.location='/pages/admin.php'</script>";
     
      
  }
  else{
    echo "<script>alert('Please verfiy your login information.')</script>";
  } 
}

/*
if($_REQUEST['username']!=NULL&&trim($_REQUEST['username'])!='')
{
	$username = $_REQUEST['username'];
	$pwd = $_REQUEST['pwd'];

	//echo $username."<br>";
	$sha1pwd =  sha1($pwd);

	//$sql = "SELECT a.username,a.password,a.groupid FROM qp_users a where a.username='".$username."' and a.password='".$sha1pwd."'";

	$data = new Data($db,"qp_users","id");
	$data->set("username",$username);
	$data->set("password",$sha1pwd);
	$datamsg = $data->find();
	$totalrows = $datamsg->getSize(); // return the total rows of the results

	if($totalrows!=1)
	{
		echo "<script>alert('ÓÃ»§Ãû»òÕßÃÜÂë´íÎó')</script>";
	}
	else if($totalrows==1)
	{
		$data = $datamsg->getData(0);
    $id = $data->getInt("id");
		$session = new Session();
		$session->setsession('username',$username);
    $session->setsession('id',$id);
    //echo $_SESSION['username']."@@@";
    //echo "<script>alert('µÇÂ¼³É¹¦£¡')</script>";
		echo "<script>window.location='/pages/admin.php'</script>";

	}
	
}
*/

?>

<!DOCTYPE html>
<html>	
<head>
<title>Login</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta http-equiv="Content-Type" content="text/html;charset=<?php echo QuickFormConfig::$encode?>" >
<script type="application/x-javascript"> addEventListener("load", function() { setTimeout(hideURLbar, 0); }, false); function hideURLbar(){ window.scrollTo(0,1); } </script>
<link href="/js/adminlogin/css/style.css" rel='stylesheet' type='text/css' />
<!--webfonts-->

<!--//webfonts-->
<script type="text/javascript" src="/js/jquery-1.11.1.min.js"></script>
</head>
<body>
<script>$(document).ready(function(c) {
	$('.close').on('click', function(c){
		$('.login-form').fadeOut('slow', function(c){
	  		$('.login-form').remove();
		});
	});	  
});
</script>
 <!--SIGN UP-->
 
 <h1>QuickPlus</h1>
<div class="login-form">
	<!--
		<div class="head-info">
			
			
		</div>
	-->
			<div class="clear"> </div>

			<form action="#" id="userlogin" name="userlogin" method="post">
					<input id='username' name='username' type="text" class="text" value="Username" onfocus="this.value = '';" onblur="if (this.value == '') {this.value = 'Username';}" >
						
					<input id='pwd' name='pwd' type="password" value="Password" onfocus="this.value = '';" onblur="if (this.value == '') {this.value = 'Password';}">
						

					<div class="signin">
						<input type="submit" value="Login" >
					</div>
			</form>
	
</div>

</body>
</html>