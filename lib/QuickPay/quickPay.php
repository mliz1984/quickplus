<?php 
		require_once(dirname(__FILE__)."/quickPayConfig.php");
		class QuickPay extends QuickPayConfig
		{
			
			public static function getPaymentAdapter($currency=null,$method=null,$isTest=false)
			{
				$result = null;
				if($currency==null&&trim($currency)=="")
				{
					$currency = self::$defaultCurrency;
				}
				if($method==null&&trim($method)=="")
				{
					$method = self::$defaultPaymentMethod;
				}
				$methodInfo = self::$paymentMethod[$method];
				if(is_array($methodInfo))
				{
					$adapter =$methodInfo["adapter"]; 
					$adapterSrc = $methodInfo["adapterSrc"];

					require_once($_SERVER['DOCUMENT_ROOT'].$adapterSrc);
					$result = new $adapter($methodInfo,$currency,$isTest);
				}
				return $result;
			}
		}
	  /*  $adapter = QuickPay::getPaymentAdapter();
		$item = Array('name'=>"TEST","description"=>"TEST DESC","unit_price"=>0.01,"quantity"=>2,"id"=>11);
		$items = Array($item);
		$options = Array("return_url"=>"http://kayascom.com/include/QuickPay/1.php","cancel_return_url"=>"http://kayascom.com/include/QuickPay/1.php","description"=>"JAJAJA","transactionId"=>2222);
		$url = $adapter->purchaseByPaypalExpress(0.02,$items,$options);*/
	
?>