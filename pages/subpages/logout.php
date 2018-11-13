<?php
require($_SERVER['DOCUMENT_ROOT']."/vendor/autoload.php");
require_once($_SERVER['DOCUMENT_ROOT']."/lib/parameters.php");
require_once($_SERVER['DOCUMENT_ROOT'] . "/class/session.php");
use Quickplus\Lib\QuickFormConfig;
use Quickplus\Lib\QuickLoginManager;
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