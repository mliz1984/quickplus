<?php 
require_once(dirname(__FILE__)."/omniPayAdapter.php");
 class ConvergeAdapter extends OmniPayAdapter
 {
 	        protected function getCCResultArray($order_id,$response)
	        {
	        	$result = Array();
	        	$result["message"] = $response->getMessage();	
	        	$result["result"] = $response->isSuccessful();
	        	$result["params"] = $response->getData();
	        	$result["authCode"] = $result["params"]["ssl_approval_code"];
	        	$result["fraud_review"] = "";
	        	$result["orderid"] = $order_id;
	            $result["avs_result"] = $result["params"]["ssl_avs_response"];
	            $result["cvv_result"] = $result["params"]["ssl_cvv2_response"];
	        	$result["transactionid"] =$response->getTransactionReference();
	        	return $result;
	        }

	        public function verifyCC($order_id,$firstname,$lastname,$creditcardnumber,$expirymonth,$expiryyear,$cvv,$options=Array())
	        {
	        	if(is_array($options["address"]))
				{
				   $address = "";
				   if($options["address"]["address1"]!=null&&trim($options["address"]["address1"])!="")
				   {
				   		$address = $options["address"]["address1"];
				   		if($options["address"]["address2"]!=null&&trim($options["address"]["address2"])!="") 
				   		{
				   			$address = $options["address"]["address2"].'-'.$address;
				   		}
				   	
				   }
				   else if($options["address"]["street_number"]!=null&&trim($options["address"]["street_number"])!=""&&$options["address"]["street_name"]!=null&&trim($options["address"]["street_name"])!="")
				   {
				   		$address = $options["address"]["street_number"].",".$options["address"]["street_name"];
				   		if($options["address"]["appartement"]!=null&&trim($options["address"]["appartement"])!="") 
				   		{
				   			$address = $options["address"]["appartement"].'-'.$address;
				   		}
				   }
				   $options["ssl_avs_address"] = $address;
				   if($options["address"]["zip"]!=null&&trim($options["address"]["zip"])!="")
				   {
				   		$options["ssl_avs_zip"] = $options["address"]["zip"];
				   }
				}
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
					    $creditCard->setAddress1($options["ssl_avs_address"]);
					    $creditCard->setCity($options["address"]["city"]);
					    $creditCard->setState($options["address"]["state"]);
					    $creditCard->setCountry($options["address"]["country"]);
					    $creditCard->setPostcode($options["ssl_avs_zip"]);

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

	        public function purchaseByCC($amount,$order_id,$firstname,$lastname,$creditcardnumber,$expirymonth,$expiryyear,$cvv,$options=Array(), $method=null)
			{
				$options["ssl_customer_code"] = $options["cust_id"];
				if(is_array($options["address"]))
				{
				   $address = "";
				   if($options["address"]["address1"]!=null&&trim($options["address"]["address1"])!="")
				   {
				   		$address = $options["address"]["address1"];
				   		if($options["address"]["address2"]!=null&&trim($options["address"]["address2"])!="") 
				   		{
				   			$address = $options["address"]["address2"].'-'.$address;
				   		}
				   	
				   }
				   else if($options["address"]["street_number"]!=null&&trim($options["address"]["street_number"])!=""&&$options["address"]["street_name"]!=null&&trim($options["address"]["street_name"])!="")
				   {
				   		$address = $options["address"]["street_number"].",".$options["address"]["street_name"];
				   		if($options["address"]["appartement"]!=null&&trim($options["address"]["appartement"])!="") 
				   		{
				   			$address = $options["address"]["appartement"].'-'.$address;
				   		}
				   }
				   $options["ssl_avs_address"] = $address;
				   if($options["address"]["zip"]!=null&&trim($options["address"]["zip"])!="")
				   {
				   		$options["ssl_avs_zip"] = $options["address"]["zip"];
				   }
				}
				$result = Array();
				try{
						$creditCardInfo = Array(
										'firstName' => $firstname,
								        'lastName' => $lastname,
								        'number' => $creditcardnumber,
								        'expiryMonth' => $expirymonth,
								        'expiryYear' => $expiryyear,
								        'cvv'=>$cvv,
								        'address1'=>$options["ssl_avs_address"],
								        'postcode'=>$options["ssl_avs_zip"]
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
 }
?>