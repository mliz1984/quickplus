<?php namespace Omnipay\ConvergeGateway\Message;

class ConvergePurchaseRequest extends ConvergeAuthorizeRequest
{
    public function getData()
    {
        $this->transactionType = 'ccsale';

        return parent::getData();
    }
}
