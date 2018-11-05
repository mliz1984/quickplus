<?php
    require_once(dirname(__FILE__)."/baseAdapter.php");
    require_once(dirname(__FILE__)."/lib/EventDispatcher/EventSubscriberInterface.php");
    require_once(dirname(__FILE__)."/lib/EventDispatcher/EventDispatcherInterface.php");
    require_once(dirname(__FILE__)."/lib/EventDispatcher/EventDispatcher.php");
    require_once(dirname(__FILE__)."/lib/EventDispatcher/Event.php");
    require_once(dirname(__FILE__)."/lib/Guzzle/Common/ToArrayInterface.php");
    require_once(dirname(__FILE__)."/lib/Guzzle/Common/Version.php");
    require_once(dirname(__FILE__)."/lib/Guzzle/Common/Event.php");
    require_once(dirname(__FILE__)."/lib/Guzzle/Common/HasDispatcherInterface.php");
    require_once(dirname(__FILE__)."/lib/Guzzle/Common/AbstractHasDispatcher.php");
    require_once(dirname(__FILE__)."/lib/Guzzle/Common/Collection.php");
    require_once(dirname(__FILE__)."/lib/Guzzle/Common/Exception/GuzzleException.php");
    require_once(dirname(__FILE__)."/lib/Guzzle/Common/Exception/ExceptionCollection.php");
    require_once(dirname(__FILE__)."/lib/Guzzle/Common/Exception/RuntimeException.php");
    require_once(dirname(__FILE__)."/lib/Guzzle/Http/Exception/HttpException.php");
    require_once(dirname(__FILE__)."/lib/Guzzle/Http/Exception/MultiTransferException.php");
    require_once(dirname(__FILE__)."/lib/Guzzle/Http/Exception/RequestException.php");
    require_once(dirname(__FILE__)."/lib/Guzzle/Http/Exception/CurlException.php");
    require_once(dirname(__FILE__)."/lib/Guzzle/Http/QueryString.php");
    require_once(dirname(__FILE__)."/lib/Guzzle/Http/Curl/CurlVersion.php");
    require_once(dirname(__FILE__)."/lib/Guzzle/Http/Curl/CurlHandle.php");
    require_once(dirname(__FILE__)."/lib/Guzzle/Http/Curl/CurlMultiInterface.php");
    require_once(dirname(__FILE__)."/lib/Guzzle/Http/Curl/RequestMediator.php");
    require_once(dirname(__FILE__)."/lib/Guzzle/Http/Curl/CurlMulti.php");
    require_once(dirname(__FILE__)."/lib/Guzzle/Http/Curl/CurlMultiProxy.php");
    require_once(dirname(__FILE__)."/lib/Guzzle/Http/Message/Header/HeaderFactoryInterface.php");
    require_once(dirname(__FILE__)."/lib/Guzzle/Http/Message/Header/HeaderCollection.php");
    require_once(dirname(__FILE__)."/lib/Guzzle/Http/Message/Header/HeaderFactory.php");
    require_once(dirname(__FILE__)."/lib/Guzzle/Http/Message/RequestFactoryInterface.php");
    require_once(dirname(__FILE__)."/lib/Guzzle/Http/Message/MessageInterface.php");
    require_once(dirname(__FILE__)."/lib/Guzzle/Http/Message/RequestInterface.php");
    require_once(dirname(__FILE__)."/lib/Guzzle/Http/Message/EntityEnclosingRequestInterface.php");
    require_once(dirname(__FILE__)."/lib/Guzzle/Http/Message/RequestFactory.php");
    require_once(dirname(__FILE__)."/lib/Guzzle/Http/Message/Header/HeaderInterface.php");
    require_once(dirname(__FILE__)."/lib/Guzzle/Http/Message/Header.php");
    require_once(dirname(__FILE__)."/lib/Guzzle/Stream/StreamInterface.php");
    require_once(dirname(__FILE__)."/lib/Guzzle/Stream/Stream.php");
    require_once(dirname(__FILE__)."/lib/Guzzle/Http/EntityBodyInterface.php");
    require_once(dirname(__FILE__)."/lib/Guzzle/Http/EntityBody.php");
    require_once(dirname(__FILE__)."/lib/Guzzle/Http/Url.php");
    require_once(dirname(__FILE__)."/lib/Guzzle/Parser/ParserRegistry.php");
    require_once(dirname(__FILE__)."/lib/Guzzle/Http/Message/AbstractMessage.php");
    require_once(dirname(__FILE__)."/lib/Guzzle/Http/Message/Header/CacheControl.php");
    require_once(dirname(__FILE__)."/lib/Guzzle/Http/Message/Request.php");
    require_once(dirname(__FILE__)."/lib/Guzzle/Http/Message/EntityEnclosingRequest.php");
    require_once(dirname(__FILE__)."/lib/Guzzle/Http/Message/Response.php");
    require_once(dirname(__FILE__)."/lib/Guzzle/Parser/UriTemplate/UriTemplateInterface.php");
    require_once(dirname(__FILE__)."/lib/Guzzle/Parser/UriTemplate/UriTemplate.php");
    require_once(dirname(__FILE__)."/lib/HttpFoundation/ParameterBag.php");
    require_once(dirname(__FILE__)."/lib/HttpFoundation/ServerBag.php");
    require_once(dirname(__FILE__)."/lib/HttpFoundation/HeaderBag.php");
    require_once(dirname(__FILE__)."/lib/HttpFoundation/FileBag.php");
    require_once(dirname(__FILE__)."/lib/HttpFoundation/Request.php");

    require_once(dirname(__FILE__)."/lib/Guzzle/Http/ClientInterface.php");
	require_once(dirname(__FILE__)."/lib/Guzzle/Common/AbstractHasDispatcher.php");
	require_once(dirname(__FILE__)."/lib/Guzzle/Http/Client.php");
    require_once(dirname(__FILE__)."/lib/Guzzle/Http/RedirectPlugin.php");

    require_once(dirname(__FILE__)."/lib/Omnipay/Omnipay.php");
    require_once(dirname(__FILE__)."/lib/Omnipay/Common/Message/MessageInterface.php");
    require_once(dirname(__FILE__)."/lib/Omnipay/Common/Message/ResponseInterface.php");
    require_once(dirname(__FILE__)."/lib/Omnipay/Common/Message/RedirectResponseInterface.php");
    require_once(dirname(__FILE__)."/lib/Omnipay/Common/Message/RequestInterface.php");
    require_once(dirname(__FILE__)."/lib/Omnipay/Common/Message/AbstractRequest.php");
    require_once(dirname(__FILE__)."/lib/Omnipay/Common/Message/AbstractResponse.php");
    require_once(dirname(__FILE__)."/lib/Omnipay/Common/Currency.php"); 
    require_once(dirname(__FILE__)."/lib/Omnipay/Common/CreditCard.php"); 
    require_once(dirname(__FILE__)."/lib/Omnipay/Common/ItemInterface.php");
    require_once(dirname(__FILE__)."/lib/Omnipay/Common/ItemBag.php");
    require_once(dirname(__FILE__)."/lib/Omnipay/Common/Item.php");
    require_once(dirname(__FILE__)."/lib/Omnipay/Common/Helper.php");
    require_once(dirname(__FILE__)."/lib/Omnipay/Common/GatewayFactory.php");
    require_once(dirname(__FILE__)."/lib/Omnipay/Common/GatewayInterface.php");
    require_once(dirname(__FILE__)."/lib/Omnipay/Common/AbstractGateway.php");
    require_once(dirname(__FILE__)."/lib/Omnipay/Common/Exception/OmnipayException.php");
    require_once(dirname(__FILE__)."/lib/Omnipay/Common/Exception/RuntimeException.php");
    require_once(dirname(__FILE__)."/lib/Omnipay/Common/Exception/InvalidRequestException.php");
    require_once(dirname(__FILE__)."/lib/Omnipay/Common/Exception/InvalidCreditCardException.php");
    require_once(dirname(__FILE__)."/lib/Omnipay/PayPal/Message/AbstractRequest.php");
    require_once(dirname(__FILE__)."/lib/Omnipay/PayPal/Message/Response.php");
    require_once(dirname(__FILE__)."/lib/Omnipay/PayPal/Message/RefundRequest.php");
    require_once(dirname(__FILE__)."/lib/Omnipay/PayPal/Message/CaptureRequest.php");
    require_once(dirname(__FILE__)."/lib/Omnipay/PayPal/Message/ExpressAuthorizeRequest.php"); 
    require_once(dirname(__FILE__)."/lib/Omnipay/PayPal/Message/ExpressAuthorizeResponse.php"); 
    require_once(dirname(__FILE__)."/lib/Omnipay/PayPal/Message/ExpressFetchCheckoutRequest.php"); 
    require_once(dirname(__FILE__)."/lib/Omnipay/PayPal/Message/ExpressCompleteAuthorizeRequest.php"); 
    require_once(dirname(__FILE__)."/lib/Omnipay/PayPal/Message/ExpressCompletePurchaseRequest.php"); 
    require_once(dirname(__FILE__)."/lib/Omnipay/PayPal/Message/ExpressVoidRequest.php"); 
    require_once(dirname(__FILE__)."/lib/Omnipay/PayPal/PayPalItemBag.php");
    require_once(dirname(__FILE__)."/lib/Omnipay/PayPal/PayPalItem.php");
    require_once(dirname(__FILE__)."/lib/Omnipay/PayPal/ProGateway.php");

	class OmniPayAdapter extends BaseAdapter
	{
			protected $gateway = null;
			protected $currency = null; 
			protected $resultArrayMethod = "getCCResultArray";
			function __construct($methodInfo,$currency,$isTest=false)
			{
				$omniGatewaySrc = $methodInfo["omniGatewaySrc"];
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
				require_once(dirname(__FILE__)."/lib/Omnipay".$omniGatewaySrc);
				$this->setTestMode($isTest);
				$this->gateway = new $gateway();
				$this->gateway->initialize($auth);
				$this->gateway->setTestMode($this->getTestMode());
				$this->currency = $currency;
			}
			public function captureByCC($amount,$order_id,$transactionid,$authCode,$options=Array())
	        {
	        	$method = "capture";
	        	$options['amount'] = $amount;
	        	return $this->voidByCC($order_id,$transactionid,$authCode,$options,$method);
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
			public function refundByCC($amount,$refundtype,$order_id,$transactionid,$options=Array())
	        {
	        	$method = "refund";
	        	$options['amount'] = $amount;
	        	return $this->voidByCC($order_id,$transactionid,$authCode,$options,$method);
	        }
			public function voidByCC($order_id,$transactionid,$authCode,$options=Array(),$method=null)
	        {
	        	$options['order_id'] = $order_id;
	        	$options['transactionId'] = $transactionid; 
	        	if($method==null||trim($method)=="")
				{
					$method = "void";
				}
				$result = $this->gateway->$method($options)->send();
			    return $this->getCCResultArray($order_id,$result);
	        }

			public function purchaseByCC($amount,$order_id,$firstname,$lastname,$creditcardnumber,$expirymonth,$expiryyear,$cvv,$options=Array(), $method=null)
			{
				$result = Array();
				try{
						$creditCardInfo = Array(
										'firstName' => $firstname,
								        'lastName' => $lastname,
								        'number' => $creditcardnumber,
								        'expiryMonth' => $expirymonth,
								        'expiryYear' => $expiryyear,
								        'cvv'=>$cvv
								        );
						
						$creditCard = new Omnipay\Common\CreditCard($creditCardInfo);
						$options["amount"] = $amount;
						$options["ssl_invoice_number"] = $order_id;
						$options["card"] = $creditCard;
						if($method==null||trim($method)=="")
						{
							$method = "purchase";
						}
							
						$result = $this->gateway->$method($options)->send();

						return $this->getCCResultArray($order_id,$result);
					}
					catch(Exception $e)
					{
						$result = Array(
										  "result" => false,
										  "message" => $e->getMessage()
										);
						return $result;
					}
			}
			
			public function authorizeByCC($amount,$order_id,$firstname,$lastname,$creditcardnumber,$expirymonth,$expiryyear,$cvv,$options=Array())
			{
			    return $this->purchaseByCC($amount,$order_id,$firstname,$lastname,$creditcardnumber,$expirymonth,$expiryyear,$cvv,$options,"authorize");
			}

 			public function verifyCC($order_id,$firstname,$lastname,$creditcardnumber,$expirymonth,$expiryyear,$cvv,$options=Array())
	        {
	        	$result = Array();
				try{
						$creditCardInfo = Array(
										'firstName' => $firstname,
								        'lastName' => $lastname,
								        'number' => $creditcardnumber,
								        'expiryMonth' => $expirymonth,
								        'expiryYear' => $expiryyear,
								        'cvv'=>$cvv
								        );
						
						$creditCard = new Omnipay\Common\CreditCard($creditCardInfo); 
						$options["ssl_invoice_number"] = $order_id;
						$options["card"] = $creditCard;
							
						$result = $this->gateway->verification($options)->send();

						return $this->getCCResultArray($order_id,$result);
					}
					catch(Exception $e)
					{
						$result = Array(
										  "result" => false,
										  "message" => $e->getMessage()
										);
						return $result;
					}
	        }

			public function purchaseByPaypalExpress($amount,$items=Array(),$options=Array(),$getUrl=false)
			{
				return $this->submitByPaypalExpress($amount,"purchase",$items,$options,$getUrl);
			}

			public function authorizeByPaypalExpress($amount,$items=Array(),$options=Array(),$getUrl=false)
			{
				return $this->submitByPaypalExpress($amount,"authorize",$items,$options,$getUrl);
			}

			protected function submitByPaypalExpress($amount,$method,$items=Array(),$options=Array(),$getUrl=false)
			{
				 $amount = floatval($amount);
	        	 $newItems = Array();
	        	 foreach($items as $item)
	        	 {
	        	 	if(!isset($item["price"])&&isset($item["unit_price"]))
	        	 	{
	        	 		$item["price"] = $item["unit_price"];
	        	 		unset($item["unit_price"]);
	        	 		$newItems[] = $item;
	        	 	}
	        	}
	        	if(!isset($options["returnUrl"])&&isset($options["return_url"]))
	        	{
	        		$options["returnUrl"] = $options["return_url"];
	        		unset($options["return_url"]);
	        	}
	        	if(!isset($options["cancelUrl"])&&isset($options["cancel_return_url"]))
	        	{
	        		$options["cancelUrl"] = $options["cancel_return_url"];
	        		unset($options["cancel_return_url"]);
	        	}
	        	$options["amount"] =  $amount;
	        	$options["items"] =  $newItems;
				$options["currency"] = $this->currency;
                if($_REQUEST["token"]==null||trim($_REQUEST["token"])=="")
	             {
	             	$result = $this->gateway->$method($options)->send();
	             	$url =  $result->getRedirectUrl();
	             	if($getUrl)
	             	{
	             		return $url;
	             	}
	             	echo "<script>window.location.href='".$url."';</script>";
	             }
              
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

			public function refundByPaypal($amount,$refundtype,$transactionid,$options=Array())
	        {
	        	if(trim($refundtype)=="Full")
	        	{
	        		$amount = 0;
	        	}
	        	$options = Array();
	        	$options["amount"] = $amount;
	        	$options["currency"] = $this->currency;
	        	$options["transactionReference"] = $transactionid;
			    $response = $this->refund($options)->send();
	        	$result = $this->getPayalResultArray($order_id,$response);
				return $result;
	        }
            
            public function captureByPaypal($amount,$authorization,$options=Array())
	        {
	        	$options = Array();
	        	$options["amount"] = $amount;
	        	$options["currency"] = $this->currency;
	        	$options["transactionReference"] = $authorization;
	        	$response = $this->capture($options)->send();
	        	$result = $this->getPayalResultArray($order_id,$response);
				return $result;
	        }

			public function voidByPaypal($authorization, $options = array())
	        {
	        	$option["transactionReference"] = $authorization;
	        	$response = $this->gateway->void($option)->send();
	        	$result = $this->getPayPalResultArray($response);
				return $result;
	        }


			 public function PaypalExpressPurchaseComfirmation($tokey,$payerid)
	        {
	        	return $this->PaypalExpressComfirmation($tokey,$payerid,"completePurchase");
	        }

			 public function PaypalExpressAuthorizeComfirmation($tokey,$payerid)
	        {
	        	return $this->PaypalExpressComfirmation($tokey,$payerid,"completeAuthorize");
	        }
			protected function PaypalExpressComfirmation($token,$payerId,$method)
	        {
	        	$info = $this->getPaypalExpressDetail($token,$payerId);
	        	$options = Array();
	        	$options["token"] = $token;
	        	$options["payerID"] =$payerId;
	        	$options["amount"] = $info["amount"];
	        	$options["currency"] = $this->currency;
	        	$response = $this->gateway->$method($options)->send();
	        	return $this->getPayPalResultArray($response);
 	        }
			public function getPaypalExpressDetail($tokenid,$payerId)
	        {
	        	$tmp["token"] = $tokenid; 
	        	$response = $this->gateway->fetchCheckout($tmp)->send();
	        	return $this->getPayPalResultArray($response);
	        }
	       
	        protected function getCCResultArray($order_id,$response)
	        {

	        	$result = Array();
	        	$result["message"] = $response->getMessage();	
	        	$result["result"] = $response->isSuccessful();
	        	$result["orderid"] = $order_id;
	        	$result["authCode"] = "";
	            $result["fraud_review"] = "";
	            $result["avs_result"] = "";
	            $result["cvv_result"] = "";
	        	$result["params"] = $response->getData();
	        	$result["transactionid"] =$response->getTransactionReference();
	        	return $result;
	        }

	        protected function getPayPalResultArray($paypalResponse)
	        {
	        	    $result = Array();
	        	    $data = $paypalResponse->getData();
	        	 	$result["email"] = $data["email"];
	        	 	$name =  $data["FIRSTNAME"];
	        	 	if($data["MIDDLENAME"]!=null&&trim($data["MIDDLENAME"])!="")
	        	 	{
	        	 		$name .= " ".$data["MIDDLENAME"]; 
	        	 	}
	        	 	$name .= " ".$data["LASTNAME"]; 
	        	 	$result["name"] = $name;
	        	 	$result["token"] = $data["TOKEN"];
	        	 	$result["payerid"] = $data["PAYERID"];
	        	 	$result["payerCountry"] = $data["COUNTRYCODE"];
	        	 	$result["amount"] = $data["AMT"];
	        	 	$result["address"] =  array(
				            'name' => $data['SHIPTONAME'],
				            'address1' => $data['SHIPTOSTREET'],
				            'address2' => $data['SHIPTOSTREET2'],
				            'city' => $data['SHIPTOCITY'],
				            'state' => $data['SHIPTOSTATE'],
				            'zip' => $data['SHIPTOZIP'],
				            'country_code' => $data['SHIPTOCOUNTRYCODE'],
				            'country' => $data['SHIPTOCOUNTRYNAME'],
				            'address_status' => $data['ADDRESSSTATUS']
				        );
	        	 	$note = null;
	        	 	if($data["note"]!=null&&trim($data["note"])!="")
	        	 	{
	        	 		$note = $data["note"];
	        	 	}
	        	 	$result["note"] = $note;
	        	 	$result["params"] = $data; 
	        	 	$result["message"] = $paypalResponse->getMessage();	
					$result["result"] = $paypalResponse->isSuccessful();
					$result["authCode"] = $paypalResponse->getTransactionReference();
					
					$result["authorization"] = $paypalResponse->getTransactionReference();
					return $result;
	        }

	}
?>