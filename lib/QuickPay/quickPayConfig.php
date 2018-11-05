<?php  
	class QuickPayConfig 
	{
		protected static $defaultPaymentMethod = "Paypal-omnipay";
		protected static $defaultCurrency = 'CAD';
		protected static $paymentMethod = Array(
			 "Moneris" => Array(
			 					
			 						"adapter"=>"AktiveMerchantApdapter",
			 						"adapterSrc"=> "/include/QuickPay/aktiveMerchantAdapter.php",
			 						"gateway"=>"Moneris",
			 						"auth"=>Array(
			 							"store_id"=>"",
			 							"api_token"=>"",
			 						 )
			 				   ),
			

			"Paypal" => Array(
									"adapter"=>"AktiveMerchantApdapter",
			 						"adapterSrc"=> "/include/QuickPay/aktiveMerchantAdapter.php",
			 						"gateway"=>"PaypalExpress",
			 						"auth"=>Array(
			 							"login"=>"",
			 							"password"=>"",
			 							"signature"=>
			 							""
			 						 )
							 ),
			"Paypal-omnipay" => Array(
									"adapter"=>"OmniPayAdapter",
									"adapterSrc"=> "/include/QuickPay/omniPayAdapter.php",
									"gateway"=>"Omnipay\PayPal\ExpressGateway", 
									"omniGatewaySrc"=>"/PayPal/ExpressGateway.php",
									"auth"=>Array(
			 							"username"=>"",
			 							"password"=>"",
			 							"signature"=>
			 							"",
			 						 )
								),
			   "PaypalPro" => Array(
									"adapter"=>"OmniPayAdapter",
									"adapterSrc"=> "/include/QuickPay/omniPayAdapter.php",
									"gateway"=>"Omnipay\PayPal\ProGateway", 
									"omniGatewaySrc"=>"/PayPal/ProGateway.php",
									"gatewayLibSrc"=>Array(
									      "/include/QuickPay/lib/Omnipay/PayPal/Message/ProAuthorizeRequest.php",
									      "/include/QuickPay/lib/Omnipay/PayPal/Message/ProPurchaseRequest.php",
									      "/include/QuickPay/lib/Omnipay/PayPal/Message/CaptureRequest.php",
									      "/include/QuickPay/lib/Omnipay/PayPal/Message/RefundRequest.php",
									      "/include/QuickPay/lib/Omnipay/PayPal/Message/FetchTransactionRequest.php",
						
										),
									"auth"=>Array(
			 							"username"=>"",
			 							"password"=>"",
			 							"signature"=>
			 							"",
			 						 )
								),
				"Converge" => Array(
									"adapter"=>"ConvergeAdapter",
									"adapterSrc"=> "/include/QuickPay/convergeAdapter.php",
									"gateway"=>"Omnipay\Elavon\ConvergeGateway", 
									"omniGatewaySrc"=>"/Elavon/ConvergeGateway.php",
									"gatewayLibSrc"=>Array(
									      "/include/QuickPay/lib/Omnipay/Elavon/Message/ConvergeAbstractRequest.php",
									      "/include/QuickPay/lib/Omnipay/Elavon/Message/ConvergeAuthorizeRequest.php",
									      "/include/QuickPay/lib/Omnipay/Elavon/Message/ConvergePurchaseRequest.php",
									       "/include/QuickPay/lib/Omnipay/Elavon/Message/ConvergeVoidRequest.php",
									        "/include/QuickPay/lib/Omnipay/Elavon/Message/ConvergeRefundRequest.php",
									        "/include/QuickPay/lib/Omnipay/Elavon/Message/ConvergeCaptureRequest.php",
									        "/include/QuickPay/lib/Omnipay/Elavon/Message/ConvergeVerificationRequest.php",
									      "/include/QuickPay/lib/Omnipay/Elavon/Message/ConvergeResponse.php",
										),
									"auth"=>Array(
			 							"username"=>"",
			 							"password"=>"",
			 							"merchantId"=>
			 							"",
			 						 )
								)
			);

	



	}
 ?>