<?php

require_once($_SERVER['DOCUMENT_ROOT']."/lib/parameters.php");
require_once($_SERVER['DOCUMENT_ROOT']."/lib/dbmodule.php"); 
require_once($_SERVER['DOCUMENT_ROOT']."/lib/quickFormDrawer.php"); 
require_once($_SERVER['DOCUMENT_ROOT']."/lib/quickFormConfig.php");
require_once($_SERVER['DOCUMENT_ROOT']."/class/session.php");       
$db = new  QuickFormConfig::$SqlType();
$loginmanager = QuickLoginManager::getQuickLoginManager();
$loginmanager->logout();
echo "<script>alert('Bye!')</script>";
echo "<script>window.location='/index.php'</script>";
?>

<!DOCTYPE html>
<html>

<head>


<meta http-equiv="Content-Type" content="text/html;charset=<?php echo QuickFormConfig::$encode?>" >
</head>
</html>