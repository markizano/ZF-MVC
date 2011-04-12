<?php
class Kp_Service_Authorizenet extends Kp_Service_Abstract
{
    const AUTH_CAPTURE          = 'authcapture';
    const AUTH_ONLY             = 'authonly';
    const PRIOR_AUTH_CAPTURE    = 'priorauthcapture';
    const CAPTURE_ONLY          = 'captureonly';
    const CREDIT                = 'refund';
    const VOID                  = 'void';

    public function getInstance()
    {
        if(is_null(self::$_instance)) {
            self::$_instance = new self();
            self::getInstance()->init();
        }

        return self::$_instance;
    }

    public function init()
    {
        $this->setResourceName('Plugins_Authorizenet_Cim_Transaction');
        $this->setData('finalResource', Ic::get($this->getResourceName()));
    }

    // TODO: define load
    public function load($transaction)
    {
        $finalResource = $this->getData('finalResource');

        $mode = Zend_Registry::get('application')->getOption('authent_mode');

        $testMode = false;
        $gatewayConf = Zend_Registry::get('application')->getOption('gateway');
        if($mode == "test") {
            $testMode = true;
            $loginId  = $gateway['authorizenet_userid'];
            $key      = $gateway['authorizenet_key'];
        } else {
            $loginId  = $gateway['authorizenet_test_userid'];
            $key      = $gateway['authorizenet_test_key'];
        }
        $config['loginId'] = $loginId;
        $config['tranKey'] = $key;
        $config['order'] = array(
                'invoiceNumber'         => $transaction->getId(),
                'purchaseOrderNumber'   => '',
                'description'           => ''
            );

        switch(strtolower($transaction->getType())) {
            case self::AUTH_CAPTURE :
                $this->setResource($finalResource::getAuthCapture($config));
            break;
        }

        $this->getResource()->setAmount($transaction->getAmount());
        $this->getResource()->setCardCode($transaction->getCvn());

        return self::getInstance();
    }

    // TODO: define process
    public function process($transaction)
    {
        $this->getResource()->process($transaction);
        return self::getInstance();
    }

    // TODO: define complete
    public function complete($transaction)
    {
         return self::getInstance();
    }

    // TODO: define shutdown
    public function shutdown()
    {
         return self::getInstance();
    }

    // TODO: define __destruct
    public function __destruct()
    {
    }

    // TODO: define _beforeLoad
    public function _beforeLoad()
    {
         return self::getInstance();
    }

    // TODO: define _beforeProcess
    public function _beforeProcess()
    {
         return self::getInstance();
    }

    // TODO: define _beforeComplete
    public function _beforeComplete()
    {
         return self::getInstance();
    }

    // TODO: define _beforeShutdown
    public function _beforeShutdown()
    {
         return self::getInstance();
    }

    // TODO: define _beforeDestruct
    public function _beforeDestruct()
    {
         return self::getInstance();
    }

    // TODO: define _afterInit
    public function _afterInit()
    {
         return self::getInstance();
    }

    // TODO: define _afterLoad
    public function _afterLoad()
    {
         return self::getInstance();
    }

    // TODO: define _afterComplete
    public function _afterComplete()
    {
         return self::getInstance();
    }

    // TODO: define _afterProcess
    public function _afterProcess()
    {
         return self::getInstance();
    }

    // TODO: define _afterComplete
    public function _afterComplete()
    {
         return self::getInstance();
    }
}