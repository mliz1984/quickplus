<?php 
	require_once(dirname(__FILE__)."/quickPay.php");

	class QuickBatchPay 
	{
		protected $paymentAdapter = null;
		protected $paymentCol = Array(
									"amount"=>"amount",
									"orderid"=>"orderid",
									"firstname"=>"firstname",
									"lastname"=>"lastname",
									"creditcardnumber"=>"creditcardnumber",
									"expirymonth"=>"expirymonth",
									"expiryyear"=>"expiryyear",
									"cvv"=>"cvv",
									"paymentid" => "paymentid",
									"options" =>"options"
										);
		protected $paymentList = Array();
		public function getPaymentAdapter($paymentid,$paymentDetail)
		{
			return $this->paymentAdapter;
		}

		function __construct($currency=null,$method=null,$isTest=false)
		{
			$this->paymentAdapter = QuickPay::getPaymentAdapter($currency,$method,$isTest);
		}

		public function getPaymentList()
		{
			return $this->paymentList;
		}

		public function setPaymentCol($col,$value)
		{
			$this->paymentCol[$col] = $value;
			return $this;
		}
        public function addPayment($paymentid,$amount,$orderid,$firstname,$lastname,$creditcardnumber,$expirymonth,$expiryyear,$cvv,$options=Array())
        {
        							 $pay= Array(
												"amount"=>$amount,
												"orderid"=>$orderid,
												"firstname"=>$firstname,
												"lastname"=>$lastname,
												"creditcardnumber"=>$creditcardnumber,
												"expirymonth"=>$expirymonth,
												"expiryyear"=>$expiryyear,
												"cvv"=>$cvv,
												"paymentid" => $paymentid,
												"options" =>$options
												);
        							 	if(!is_array($pay["options"]))
						        		{
						        			$pay["options"] = Array();
						        		}
						        	 $this->paymentList[$paymentid] = $pay;
						        	 return $this;
        }

        public function addPaymentList($paymentList,$options=Array())
        {
        	foreach($paymentList as $payment)
        	{
        		$pay = Array();
        		$paymentidCol = $this->paymentCol["paymentid"];

        		$paymentid =  $payment[$paymentidCol];
        		$pay["options"] = $options;
        		foreach($this->paymentCol as $col => $pointCol)
        		{
        			$pay[$col] = $payment[$pointCol];
        		}
        		if(!is_array($pay["options"]))
        		{
        			$pay["options"] = Array();
        		}
        		$this->paymentList[$paymentid] = $pay;
        	}
        	return $this;
        }

        public function afterMakePayment($paymentid,$paymentDetail,$paymentResult)
        {
        		return true;
        }

        public function makeBatchPayment($resultOnly=true)
        { 

        	$result = Array();
        	
        	foreach($this->paymentList as $paymentid => $paymentDetail)
        	{
        		
        		$paymentAdapter = $this->getPaymentAdapter($paymentid,$paymentDetail);
        	     
        		$paymentResult = $paymentAdapter->purchaseByCC($paymentDetail["amount"],$paymentDetail["orderid"],$paymentDetail["firstname"],$paymentDetail["lastname"],$paymentDetail["creditcardnumber"],$paymentDetail["expirymonth"],$paymentDetail["expiryyear"],$paymentDetail["cvv"],$paymentDetail["options"]);
  
        		$this->afterMakePayment($paymentid,$paymentDetail,$paymentResult);
        		if($resultOnly)
        		{
        				$result[$paymentid] = $paymentResult;
        		}
        		else
        		{
        				$result[$paymentid] = Array("info"=>$paymentDetail,"result"=>$paymentResult);
        		}
        	}
        	return $result;
        }


	}
