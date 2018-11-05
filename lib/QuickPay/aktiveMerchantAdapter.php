<?php 
        require_once(dirname(__FILE__)."/baseAdapter.php");
        require_once(dirname(__FILE__)."/lib/EventDispatcher/EventDispatcherInterface.php");
	    require_once(dirname(__FILE__)."/lib/EventDispatcher/EventDispatcher.php");
	    require_once(dirname(__FILE__)."/lib/EventDispatcher/Event.php");
	    require_once(dirname(__FILE__)."/lib/AktiveMerchant/Event/RequestEvents.php");
	    require_once(dirname(__FILE__)."/lib/AktiveMerchant/Event/PreSendEvent.php");
	    require_once(dirname(__FILE__)."/lib/AktiveMerchant/Event/PostSendEvent.php");
	    require_once(dirname(__FILE__)."/lib/AktiveMerchant/Billing/Response.php");
	    require_once(dirname(__FILE__)."/lib/AktiveMerchant/Billing/AvsResult.php");
	    require_once(dirname(__FILE__)."/lib/AktiveMerchant/Billing/CvvResult.php");
	    require_once(dirname(__FILE__)."/lib/AktiveMerchant/Billing/Base.php");
		require_once(dirname(__FILE__)."/lib/AktiveMerchant/Billing/Gateway.php");
	    require_once(dirname(__FILE__)."/lib/AktiveMerchant/Common/Options.php");
	    require_once(dirname(__FILE__)."/lib/AktiveMerchant/Common/Error.php");
	    require_once(dirname(__FILE__)."/lib/AktiveMerchant/Billing/ExpiryDate.php");
	    require_once(dirname(__FILE__)."/lib/AktiveMerchant/Billing/CreditCardMethods.php");
	    require_once(dirname(__FILE__)."/lib/AktiveMerchant/Billing/CreditCard.php");
	    require_once(dirname(__FILE__)."/lib/AktiveMerchant/Common/SimpleXmlBuilder.php");
	    require_once(dirname(__FILE__)."/lib/AktiveMerchant/Http/RequestInterface.php");
	    require_once(dirname(__FILE__)."/lib/AktiveMerchant/Http/Request.php");
	    require_once(dirname(__FILE__)."/lib/AktiveMerchant/Http/AdapterInterface.php");
	    require_once(dirname(__FILE__)."/lib/AktiveMerchant/Http/Adapter/cUrl.php");
	    require_once(dirname(__FILE__)."/lib/AktiveMerchant/Http/Adapter/Exception.php");
	    require_once(dirname(__FILE__)."/lib/AktiveMerchant/Http/Adapter/Exception.php");
	 	require_once(dirname(__FILE__)."/lib/AktiveMerchant/Http/Adapter/Exception.php");
 		require_once(dirname(__FILE__)."/lib/AktiveMerchant/Billing/Interfaces/Charge.php");
 		require_once(dirname(__FILE__)."/lib/AktiveMerchant/Billing/Interfaces/Credit.php");
 		require_once(dirname(__FILE__)."/lib/AktiveMerchant/Billing/Gateways/Paypal/PaypalCommon.php");
 		require_once(dirname(__FILE__)."/lib/AktiveMerchant/Billing/Gateways/Paypal/PaypalExpressResponse.php");

	    use AktiveMerchant\Billing\Base;	

		class AktiveMerchantApdapter extends BaseAdapter
		{
			protected $gateway = null;
			protected $currency = null;

			function __construct($methodInfo,$currency,$isTest=false)
			{
				$gateway = $methodInfo["gateway"];
				$auth =  $methodInfo["auth"];
				$gateWayLibSrc = $methodInfo["gatewayLibSrc"];
				if(is_array($gateWayLibSrc))
				{
					foreach($gateWayLibSrc as $fileSrc)
					{
						require_once($_SERVER['DOCUMENT_ROOT'].$fileSrc);
					}
				}
				require_once(dirname(__FILE__)."/lib/AktiveMerchant/Billing/Gateways/".$gateway .".php");		
				$this->setTestMode($isTest);
				if($this->getTestMode())
				{
					Base::mode("test");
				}
				else
				{
					Base::mode("live");
				}
				$classname =  "AktiveMerchant\Billing\Gateways\\".$gateway;
				$this->currency = $currency;
				$auth["currency"] = $this->currency;
				$this->gateway = new $classname($auth);
			} 

	        public function checkCC($creditcardnumber,$expirymonth,$expiryyear)
	        {
	        	
	        	return true;
	        }

	        public function getNewOrderId()
	        {
	        	return $this->gateway->generateUniqueId();
					
	        }
	        public function refundByCC($amount,$refundtype,$order_id,$transactionid,$options=Array())
	        {
	        	 $options['order_id'] = $order_id;
	        	$response = $this->refundCommon($amount,$refundtype,$transactionid,$options);
	        	$result = $this->getCCResultArray($order_id,$response);
				return $result;
	        }

	        public function refundByPaypal($amount,$refundtype,$transactionid,$options=Array())
	        {
			    $response = $this->refundCommon($amount,$refundtype,$transactionid,$options);
	        	$result = $this->getPayPalResultArray($order_id,$response);
				return $result;
	        }

	        protected function refundCommon($amount,$refundtype,$transactionid,$options=Array())
	        {
	        	    $amount = floatval($amount);
	        		$options['refund_type'] = $refundtype;
	        		$response = $this->gateway->credit($amount, $transactionid, $options);
		        	return $response;
	        }

	        public function verifyCC($order_id,$firstname,$lastname,$creditcardnumber,$expirymonth,$expiryyear,$cvv,$options=Array())
	        {
	        	$result = Array();
	        	if($this->checkCC($creditcardnumber,$expirymonth,$expiryyear))
	        	{
	        		$credit_card = new AktiveMerchant\Billing\CreditCard( 
					    array(
					        "first_name" => $firstname,
					        "last_name" => $lastname,
					        "number" => $creditcardnumber,
					        "month" => $expirymonth,
					        "year" => $expiryyear,
					        "verification_value" => $cvv
					    )
					);
					$options['order_id'] = $order_id;
					if(is_array($options["address"]))
					{
						
							if($options["address"]["street_number"]!=null&&trim($options["address"]["street_number"])!=""&&$options["address"]["street_name"]!=null&&trim($options["address"]["street_name"])!="")
							{
								$options["address"]["address1"] = $options["address"]["street_number"]." ".$options["address"]["street_name"];
								$options["street_name"] = $options["address"]["street_name"];
								$options["street_number"] = $options["address"]["street_number"];
							}
						
					
						if(!is_array($options['shipping_address']))
						{
							$options['shipping_address'] = $options["address"];
						}
						if(!is_array($options['billing_address']))
						{
							$options['billing_address'] = $options["address"];
						}
					}
					$response = $this->gateway->verificarion($credit_card, $options);
					$result = $this->getCCResultArray($order_id,$response);
					
	        	}
	        	else
	        	{
	        		$result["message"] = "Sorry Your credit card infomation is wrong ,please check it ";
	        		$result["result"] = 0;
	        	}
	        	return $result;

	        }

	        public function purchaseByCC($amount,$order_id,$firstname,$lastname,$creditcardnumber,$expirymonth,$expiryyear,$cvv,$options=Array())
	        {
	        	
	        	return $this->payByCC($amount,$order_id,$firstname,$lastname,$creditcardnumber,$expirymonth,$expiryyear,$cvv,$options,"purchase");
	        }

	        public function voidByPaypal($authorization, $options = array())
	        {
	        	$response = $this->gateway->void($authorization, $options);
	        	$result = $this->getPayPalResultArray($response);
				return $result;
	        }

	        public function voidByCC($order_id,$transactionid,$authCode,$options=Array())
	        {
	        	$options['order_id'] = $order_id;
	        	$options['transactionId'] = $transactionid; 
	        	$response = $this->gateway->void($authCode, $options);
	        	$result = $this->getCCResultArray($order_id,$response);
				return $result;
	        }

	        public function captureByPaypal($amount,$authorization,$options=Array())
	        {
	        	$response = $this->captureCommon($amount,$authCode,$options);
	        	$result = $this->getPaypalResultArray($response);
				return $result;
	        }

	        public function captureByCC($amount,$order_id,$transactionid,$authCode,$options=Array())
	        {
	        	$options['order_id'] = $order_id;
	  			$response = $this->captureCommon($amount,$authCode,$options);
	        	$result = $this->getCCResultArray($order_id,$response);
				return $result;
	        }

	        protected function captureCommon($amount,$authorization,$options=Array())
	        {
	        	$amount = floatval($amount);
	        	$response = $this->gateway->capture($amount,$authorization, $options);    
				return $response;
	        }

	        public function cancelAuthorizeByPaypal($amount,$authorization)
	        {
	        	$result = $this->captureByPaypal($amount,$authorization);
	        	if(is_bool($result["result"])&&$result["result"])
	        	{
	        		$order_id = $result["orderid"];
	        		$authorization = $result["authorization"];
	        		$result = $this->voidByPaypal($authorization);
	        	}
	        	return $result;
	        }

	        public function cancelAuthorizeByCC($amount,$order_id,$transactionid,$authCode)
	        {
	        	$result = $this->captureByCC($amount,$order_id,$transactionid,$authCode);
	        	if(is_bool($result["result"])&&$result["result"])
	        	{
	        		$order_id = $result["orderid"];
	        		$authCode = $result["authCode"];
	        		$transactionid = $result["transactionid"];
	        		$result = $this->voidByCC($order_id,$transactionid,$authCode);
	        	}
	        	return $result;
	        }
	         
	        public function authorizeByCC($amount,$order_id,$firstname,$lastname,$creditcardnumber,$expirymonth,$expiryyear,$cvv,$options=Array())
	        {
	        	
	        	return $this->payByCC($amount,$order_id,$firstname,$lastname,$creditcardnumber,$expirymonth,$expiryyear,$cvv,$options,"authorize");
	        }

	        protected function getPayPalResultArray($paypalResponse)
	        {
	        	    $result = Array();
	        	 	$result["email"] = $paypalResponse->email();
	        	 	$result["name"] = $paypalResponse->name();
	        	 	$result["token"] = $paypalResponse->token();
	        	 	$result["payerid"] = $paypalResponse->payer_id();
	        	 	$result["payerCountry"] = $paypalResponse->payer_country();
	        	 	$result["amount"] = $paypalResponse->amount();
	        	 	$result["address"] = $paypalResponse->address();
	        	 	$result["note"] = $paypalResponse->note();
	        	 	$result["message"] = $paypalResponse->message();	
					$result["result"] = $paypalResponse->success();
					$result["authCode"] = $paypalResponse->authorization();
					$result["fraud_review"] = $paypalResponse->fraud_review();
					$result["avs_result"] = $paypalResponse->avs_result();
					$result["cvv_result"] = $paypalResponse->cvv_result();
					$result["params"] = $paypalResponse->params()->getArrayCopy();
					$result["authorization"] = $paypalResponse->authorization();
					return $result;
	        }

	        protected function getCCResultArray($order_id,$response)
	        {
	        	$result = Array();
				$result["message"] = $response->message();	
				$result["result"] = $response->success();
				$result["orderid"] = $order_id;
				$result["fraud_review"] = $response->fraud_review();
				$result["avs_result"] = $response->avs_result()->toArray();
				$result["cvv_result"] = $response->cvv_result()->toArray();
				$result["params"] = $response->params();
				$result["authCode"] = $result["params"]["auth_code"];
			    $result["transactionid"] =$result["params"]["transaction_id"];
				return $result;
	        }




	        protected function submitByPaypalExpress($amount,$method,$items=Array(),$options=Array(),$getUrl=false)
	        {
	        	 $amount = floatval($amount);
	        	 $newItems = Array();
	        	 foreach($items as $item)
	        	 {
	        	 	if(!isset($item["unit_price"])&&isset($item["price"]))
	        	 	{
	        	 		$item["unit_price"] = $item["price"];
	        	 		unset($item["unit_price"]);
	        	 		$newItems[] = $item;
	        	 	}
	        	 }
	        	 $options["items"] =  $newItems;
	        	 if($options["description"]!=null&&trim($options["description"])!=null)
	        	 {
	        	 	$options["extra_options"]["PAYMENTREQUEST_0_DESC"] = $options["description"];
	        	 }
	        	  if($options["transactionId"]!=null&&trim($options["transactionId"])!=null)
	        	 {
	        	 	$options["extra_options"]["PAYMENTREQUEST_0_INVNUM"] = $options["transactionId"];
	        	 }
	        	 if(!isset($options["return_url"])&&isset($options["returnUrl"]))
	        	{
	        		$options["return_url"] = $options["returnUrl"];
	        		unset($options["returnUrl"]);
	        	}
	        	if(!isset($options["cancel_return_url"])&&isset($options["cancelUrl"]))
	        	{
	        		$options["cancel_return_url"] = $options["cancelUrl"];
	        		unset($options["cancelUrl"]);
	        	}
	        	 if($_REQUEST["token"]==null||trim($_REQUEST["token"])=="")
	             {
	             	$result = null;
	                $result =  $this->gateway->$method($amount,$options);
	             	$url = $this->gateway->urlForToken($result->token());
	             	if($getUrl)
	             	{
	             		return $url;
	             	}
	             	echo "<script>window.location.href='".$url."';</script>";
	             }
	        }

	        public function purchaseByPaypalExpress($amount,$items=Array(),$options=Array(),$getUrl=false)
	        {
	        	return $this->submitByPaypalExpress($amount,"setupPurchase",$items,$options,$getUrl);
	        }

	        public function authorizeByPaypalExpress($amount,$items=Array(),$options=Array(),$getUrl=false)
	        {
	        	return $this->submitByPaypalExpress($amount,"setupAuthorize",$items,$options,$getUrl);
	        }

	        public function getPaypalExpressDetail($tokenid,$payerId)
	        {

	        	$paypalResponse = $this->gateway->get_details_for($tokenid,$payerId);
	        	return $this->getPayPalResultArray($paypalResponse);
	        }
	        
	        public function PaypalExpressAuthorizeComfirmation($tokey,$payerid)
	        {
	        	return $this->PaypalExpressComfirmation($tokey,$payerid,"authorize");
	        }
	        public function PaypalExpressPurchaseComfirmation($tokey,$payerid)
	        {
	        	return $this->PaypalExpressComfirmation($tokey,$payerid,"purchase");
	        }

	        protected function PaypalExpressComfirmation($token,$payerid,$method)
	        {

	        	$tmp = $this->gateway->get_details_for($token,$payerid);
	        	$amount = $tmp->amount();
	        	$paypalResponse =  $this->gateway->$method($amount,$options);
	        	$result = $this->getPayPalResultArray($paypalResponse);
	        	return $result;
	        }

	        protected function payByCC($amount,$order_id,$firstname,$lastname,$creditcardnumber,$expirymonth,$expiryyear,$cvv,$options=Array(),$method="authorize")
	        {
	        	$result = Array();
	        	$amount = floatval($amount);
	        	if($this->checkCC($creditcardnumber,$expirymonth,$expiryyear))
	        	{
	        		$credit_card = new AktiveMerchant\Billing\CreditCard( 
					    array(
					        "first_name" => $firstname,
					        "last_name" => $lastname,
					        "number" => $creditcardnumber,
					        "month" => $expirymonth,
					        "year" => $expiryyear,
					        "verification_value" => $cvv
					    )
					);
					$options['order_id'] = $order_id;
					if(is_array($options["address"]))
					{
						
							if($options["address"]["street_number"]!=null&&trim($options["address"]["street_number"])!=""&&$options["address"]["street_name"]!=null&&trim($options["address"]["street_name"])!="")
							{
								$options["address"]["address1"] = $options["address"]["street_number"]." ".$options["address"]["street_name"];
								$options["street_name"] = $options["address"]["street_name"];
								$options["street_number"] = $options["address"]["street_number"];
							}
						
					
						if(!is_array($options['shipping_address']))
						{
							$options['shipping_address'] = $options["address"];
						}
						if(!is_array($options['billing_address']))
						{
							$options['billing_address'] = $options["address"];
						}
					}
					$response = $this->gateway->$method($amount, $credit_card, $options);
					$result = $this->getCCResultArray($order_id,$response);
					
	        	}
	        	else
	        	{
	        		$result["message"] = "Sorry Your credit card infomation is wrong ,please check it ";
	        		$result["result"] = 0;
	        	}
	        	return $result;

	        }

		}



?>