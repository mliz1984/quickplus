<?php
require_once($_SERVER['DOCUMENT_ROOT']."/vendor/autoload.php");
require_once($_SERVER['DOCUMENT_ROOT'] . "/class/session.php");
use Quickplus\Lib\parameters;
use Quickplus\Lib\quickFormDrawer;
use Quickplus\Lib\QuickFormConfig as QuickFormConfig;
use Quickplus\Lib\QuickLoginManager as QuickLoginManager;
$db = new  QuickFormConfig::$SqlType();
$session = new Session();
$loginmanager = QuickLoginManager::getQuickLoginManager();
$lasturlsession = $loginmanager->getLastUrlSession();
$userinfo = $loginmanager ->getUserInfo()
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
  <div class="navbar navbar-default navbar-static-top" role="navigation">
  
    <div class = 'container-fluid'>
    <div class="navbar-header">
      <!--
           <button type="button" class="navbar-toggle" data-toggle="offcanvas" data-target=".sidebar-nav">
             <span class="icon-bar"></span>
             <span class="icon-bar"></span>
             <span class="icon-bar"></span>
           </button>
         -->
    <button type="button" class="navbar-toggle" data-toggle="collapse" 
            data-target=".navbar-ex1-collapse">
        <span class="sr-only">Toggle navigation</span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
    </button>
    <a class="navbar-brand" rel="home" href="/pages/subpages/dashboard.php" title="Brand" target="right">
        
    </a>

      
         
    </div>

       <div class="nav pull-right">

            <span>Welcome <?php  echo $userinfo['accountid'];?></span>
            <button type="button" class="btn navbar-btn" onclick="parent.left.location.href='/pages/subpages/menu.php?flag=1'">Refresh</button>
            <button type="button" class="btn btn-warning navbar-btn" onclick="$.session.remove('<?php echo $lasturlsession;?>');window.parent.window.location.href='/pages/subpages/logout.php'">Logout</button>
      </div>
       </div>
     
   </div>
   
 </body>

</html>

