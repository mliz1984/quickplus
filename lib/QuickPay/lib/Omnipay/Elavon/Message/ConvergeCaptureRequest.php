<?php namespace Omnipay\Elavon\Message;

class ConvergeCaptureRequest extends ConvergeRefundRequest
{
    protected $transactionType = 'cccomplete';
}
    