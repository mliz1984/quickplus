<?php
require("/vendor/autoload.php");
// Start the session
require_once($_SERVER['DOCUMENT_ROOT']."/lib/parameters.php");
use \Quickplus\Lib\QuickFormConfig as QuickFormConfig;
require_once($_SERVER['DOCUMENT_ROOT'] . "/class/session.php");
use \Quickplus\Lib\QuickLoginManager as QuickLoginManager;
$db = new  QuickFormConfig::$SqlType();
if($_REQUEST['username']!=NULL&&trim($_REQUEST['username'])!='')
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