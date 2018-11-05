<?php namespace Omnipay\Elavon;

use Omnipay\Common\AbstractGateway;

/**
 * Elavon's Converge Gateway
 *
 * @link https://www.myvirtualmerchant.com/VirtualMerchant/
 */
class ConvergeGateway extends AbstractGateway
{
    public function getName()
    {
        return 'Converge';
    }

    public function getDefaultParameters()
    {
        return array(
            'merchantId' => '',
            'username' => '',
            'password' => '',
            "ssl_show_form" => "false",
            "ssl_result_format" => "ASCII"  
        );
    }

    public function getMerchantId()
    {
        return $this->getParameter('merchantId');
    }

    public function setMerchantId($value)
    {
        return $this->setParameter('merchantId', $value);
    }

    public function getUsername()
    {
        return $this->getParameter('username');
    }

    public function setUsername($value)
    {
        return $this->setParameter('username', $value);
    }

    public function getPassword()
    {
        return $this->getParameter('password');
    }

    public function setPassword($value)
    {
        return $this->setParameter('password', $value);
    }

    /**
     * @param array $parameters
     * @return \Omnipay\Elavon\Message\AuthorizeRequest
     */
    public function authorize(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\Elavon\Message\ConvergeAuthorizeRequest', $parameters);
    }

    /**
     * @param array $parameters
     * @return \Omnipay\Elavon\Message\PurchaseRequest
     */
    public function purchase(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\Elavon\Message\ConvergePurchaseRequest', $parameters);
    }

    public function void(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\Elavon\Message\ConvergeVoidRequest', $parameters);
    }

    public function refund(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\Elavon\Message\ConvergeRefundRequest', $parameters);
    }

    public function capture(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\Elavon\Message\ConvergeCaptureRequest', $parameters);
    }

     public function verification(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\Elavon\Message\ConvergeVerificationRequest', $parameters);
    }


}
