<?php 
    namespace Omnipay\Elavon\Message;
	
	class ConvergeVerificationRequest extends ConvergeAbstractRequest
	{
		    protected $transactionType = 'ccverify';
		   
		    public function getData()
		    {
		        $this->validate('card');
		        $this->getCard()->validate();

		        $data = array(
		            'ssl_transaction_type' => $this->transactionType,
		            'ssl_card_number' => $this->getCard()->getNumber(),
		            'ssl_exp_date' => $this->getCard()->getExpiryDate('my'),
		            'ssl_cvv2cvc2' => $this->getCard()->getCvv(),
		            'ssl_cvv2cvc2_indicator' => ($this->getCard()->getCvv()!=null&&trim($this->getCard()->getCvv())!="") ? 1 : 0,
		            'ssl_first_name' => $this->getCard()->getFirstName(),
		            'ssl_last_name' => $this->getCard()->getLastName(),
		            'ssl_avs_zip' => $this->getCard()->getPostcode(),
		            'ssl_avs_address' => $this->getCard()->getAddress1(),
		            'ssl_address2' => $this->getCard()->getAddress2(),
		            'ssl_city' => $this->getCard()->getCity(),
		            'ssl_state' => $this->getCard()->getState(),
		            'ssl_country' => $this->getCard()->getCountry()
		        );

		        return array_merge($this->getBaseData(), $data);
		    }

    public function sendData($data)
    {
        $httpResponse = $this->httpClient->post($this->getEndpoint() . '/process.do', null, http_build_query($data))
            ->setHeader('Content-Type', 'application/x-www-form-urlencoded')
            ->send();
        return $this->createResponse($httpResponse->getBody());
    }
	}
?>