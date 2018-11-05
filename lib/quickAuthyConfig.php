<?php 
 	require_once(dirname(__FILE__)."/guzzle/autoloader.php");
 	require_once(dirname(__FILE__)."/authy/AuthyApi.php");
 	require_once(dirname(__FILE__)."/authy/AuthyResponse.php");
 	require_once(dirname(__FILE__)."/authy/AuthyFormatException.php");
 	require_once(dirname(__FILE__)."/authy/AuthyToken.php");
 	require_once(dirname(__FILE__)."/authy/AuthyUser.php");

 	 class QuickAuthyConfig extends Authy\AuthyApi
 	 {
 	 	protected $apitoken = "OJCyAlqfHsdPoQfeZrGo4ly4MAVzFY6c";
 	 	
 	 	
 	 }
 ?>