<?php
/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category   Zend
 * @package    Zend_Service
 * @subpackage AuthorizeNet
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

/**
 * Payment profiles for the customer profile.
 * Multiple instances of this element may be submitted to create multiple payment profiles
 * for the customer profile.
 *
 * @category   Zend
 * @package    Zend_Service
 * @subpackage AuthorizeNet
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
abstract class Kp_Plugins_Authorizenet_Cim_Data_PaymentProfile extends Kp_Plugins_Authorizenet_Cim_Data_Abstract
{
    /**
     * One of individual | business
     * @var string
     */
    protected $_customerType = 'individual';

    /**
     * Billing address
     * @var Kp_Plugins_Authorizenet_Cim_Data_CustomerAddress
     */
    protected $_billTo = null;

    /**
     * Contains payment profile information for the customer profile.
     * Can contain creditCard or bankAccount
     * @var Kp_Plugins_Authorizenet_Cim_Data_Payment
     */
    protected $_payment = null;

    /**
     * Payment gateway assigned ID associated with the customer payment profile
     * Used on payment profile update ONLY
     * @var int
     */
    protected $_customerPaymentProfileId = null;

    /**
     * List of expected arguments and their deafults (to be passed via $config in __construct())
     * @var array
     */
    protected $_expectedArgs = array(
        'customerType'  => 'individual',
        'billTo'        => null,
        'payment'       => null,
        'customerPaymentProfileId' => null
    );

    /**
     * Init container
     * @throws  Kp_Plugins_Authorizenet_Exception
     * @return  void
     */
    public function init()
    {
        // nested types require special processing
        if(isset($this->_config['billTo'])) {
            $this->setBillTo($this->_config['billTo']);
        }

        if(isset($this->_config['payment'])) {
            $this->setPayment($this->_config['payment']);
        }
    }

    /**
     * Sets BillTo info
     *
     * @param   mixed $billTo   Billing details as array or Kp_Plugins_Authorizenet_Cim_Data_BillTo
     * @throws  Kp_Plugins_Authorizenet_Exception
     * @return  Kp_Plugins_Authorizenet_Cim_Data_PaymentProfile
     */
    public function setBillTo($billTo)
    {
        if($billTo instanceof Kp_Plugins_Authorizenet_Cim_Data_CustomerAddress) {
            $this->_billTo = $billTo;
        } else {
            $this->_billTo = Kp_Plugins_Authorizenet_Cim_Data::factory('BillTo', $billTo, $this->_adapter);
        }
        return $this;
    }

    /**
     * Sets Payment info
     *
     * @param   mixed $billTo   Billing details as array or Kp_Plugins_Authorizenet_Cim_Data_BillTo
     * @throws  Kp_Plugins_Authorizenet_Exception
     * @return  Kp_Plugins_Authorizenet_Cim_Data_PaymentProfile
     */
    public function setPayment($payment)
    {
        if( ($payment instanceof Kp_Plugins_Authorizenet_Cim_Data_PaymentCreditCard) ||
            ($payment instanceof Kp_Plugins_Authorizenet_Cim_Data_PaymentBankAccount) ) {
            $this->_payment = $payment;
        } else {
            if(isset($payment['creditCard'])) {
                $this->_payment = Kp_Plugins_Authorizenet_Cim_Data::factory('PaymentCreditCard', $payment['creditCard'], $this->_adapter);
            } elseif (isset($payment['bankAccount'])) {
                $this->_payment = Kp_Plugins_Authorizenet_Cim_Data::factory('PaymentBankAccount', $payment['bankAccount'], $this->_adapter);
            } else {
                require_once 'Zend/Service/AuthorizeNet/Exception.php';
                throw new Kp_Plugins_Authorizenet_Exception("Unsupported payment type");
            }
        }
        return $this;
    }

    /**
     *
     * @param string $customerType  One of: individual|business
     * @return Kp_Plugins_Authorizenet_Cim_Data_PaymentProfile
     */
    public function setCustomerType($customerType) {
        $this->_customerType = $customerType;
        return $this;
    }

    /**
     * Current payment profile ID
     * @param int $customerPaymentProfileId
     */
    public function setCustomerPaymentProfileId($customerPaymentProfileId) {
        $this->_customerPaymentProfileId = $customerPaymentProfileId;
        return $this;
    }


}




