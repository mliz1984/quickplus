<?php namespace Omnipay\Elavon\Message;

class ConvergeVoidRequest extends ConvergeAbstractRequest
{
    protected $transactionType = 'ccvoid';

    public function getData()
    {
        $this->validate('transactionId');
        $data = array(
            'ssl_transaction_type' => $this->transactionType,   
            'ssl_txn_id' => $this->getTransactionId()           
        );
        //print_r($this->getBaseData());
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
    