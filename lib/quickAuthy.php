<?php 
namespace Quickplus\Lib;
 class QuickAuthy extends QuickAuthyConfig
 {

 	function __construct()
 	{
 		parent::__construct($this->apitoken);
 		$this->rest->setDefaultOption('verify', false);
 	}

 	public function getVerificationSmsCode($phonenumber,$language="en",$countryCode="1")
 	{

 		$response =  $this->phoneVerificationStart($phonenumber, $countryCode, 'sms', $language);
 		return $response->ok();
 	}

 	public function getVerificationCallCode($phonenumber,$language="en",$countryCode="1")
 	{

 		$response =  $this->phoneVerificationStart($phonenumber, $countryCode, 'call', $language);
 		return $response->ok();
 	}

 	public function verifyCode($phonenumber,$code,$countryCode="1")
 	{
 		
 		$response = $this->phoneVerificationCheck($phonenumber, $countryCode, $code);
 		return $response->ok();
 	}

    public function userRegister($phonenumber,$email="user@fastontime.com",$country_code ="1")
    {
    	$response =  $this->registerUser($email, $phonenumber, $country_code);
    	return $response->id();
    }
   
    public function userDelete($authid)
    {
    	$response = $this->deleteUser($authid);
 		return $response->ok();
    }
   
    public function verifyToken($authid,$token)
    {
    	$response =parent::verifyToken($authid,$token);
 		return $response->ok();
    }

    public function getTokenBySms($authid,$force=false)
    {
    	$opts = Array();
    	if($force)
    	{
    		$opts["force"] = "true";
    	}
    	$response = $this->requestSms($authid,$opts);
    	return $response->ok();
    }

    public function getTokenByCall($authid,$force=false)
    {
    	$opts = Array();
    	if($force)
    	{
    		$opts["force"] = "true";
    	}
    	$response = $this->phoneCall($authid,$opts);

    	return $response->ok();
    }
 }

?>