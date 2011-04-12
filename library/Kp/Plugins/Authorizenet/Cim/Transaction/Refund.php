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
 * This transaction type is used to refund a customer for a transaction that was
 * originally processed and successfully settled through the payment gateway.
 *
 * @category   Zend
 * @package    Zend_Service
 * @subpackage AuthorizeNet
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
abstract class Kp_Plugins_Authorizenet_Cim_Transaction_Refund extends Kp_Plugins_Authorizenet_Cim_Transaction_Abstract
{
    /**
     * The last four digits of the credit card number to be refunded
     * Four Xs followed by the last four digits of the credit card number to be refunded. Ex. XXXX1234
     * 
     * Required for credit card transactions if customerProfileId AND 
     * customerPaymentProfileId are not being submitted. Value submitted must be 
     * the same number used for the original transaction.
     * 
     * @var string
     */
    protected $_creditCardNumberMasked = null;

    /**
     * The last four digits of the routing number to be refunded
     *
     * Four Xs followed by the last four digits of the routing number to be refunded. Ex. XXXX1234
     * Required for electronic check transactions if customerProfileId AND customerPaymentProfileId
     * are not being submitted. Must be submitted with bankAccountNumberMasked.
     * 
     * @var string
     */
    protected $_bankRoutingNumberMasked = null;

    /**
     * The last four digits of the bank account number to be refunded
     *
     * Four Xs followed by the last four digits of the bank account to be refunded. Ex. XXXX1234
     * Required for electronic check transactions if customerProfileId AND customerPaymentProfileId
     * are not being submitted. Must be submitted with bankRoutingNumberMasked.
     * 
     * @var string
     */
    protected $_bankAccountNumberMasked = null;

    /**
     * The payment gateway assigned transaction ID of the original transaction
     * @var int
     */
    protected $_transId = null;

    /**
     * List of expected arguments and their deafults (to be passed via $config in __construct())
     * @var array
     */
    protected $_expectedArgs = array(
        'amount'                    => null,
        'tax'                       => null,
        'shipping'                  => null,
        'duty'                      => null,
        'lineItems'                 => array(),
        'customerProfileId'         => null,
        'customerPaymentProfileId'  => null,
        'customerShippingAddressId' => null,
        'creditCardNumberMasked'    => null,
        'bankRoutingNumberMasked'   => null,
        'bankAccountNumberMasked'   => null,
        'order'                     => null,
        'transId'                   => null,
    );

    /**
     * Arguments that MUST be present
     * @var array
     */
    protected $_requiredArgs = array(
        'transId',
    );

    /**
     * Validates if container has all required fields set
     *
     * @throws Kp_Plugins_Authorizenet_Exception
     * @return void
     */
    public function validate()
    {
        parent::validate();
        if(!$this->_customerProfileId && !$this->_customerPaymentProfileId) {
            if(! ($this->_creditCardNumberMasked || ($this->_bankRoutingNumberMasked && $this->_bankAccountNumberMasked))) {
                require_once 'Zend/Service/AuthorizeNet/Exception.php';
                throw new Kp_Plugins_Authorizenet_Exception(
                    'Cutomer profile ID (customerProfileId) and customer payment profile (customerPaymentProfileId)
                    are required fields. Alternatively, you can set Masked Credit Card Number (creditCardNumberMasked) for CC transactions, or
                    Bank Routing (bankRoutingNumberMasked) and Account (bankAccountNumberMasked) numbers for eCheck transactions.'
                );
            }
        }
    }

    /**
     * Current trasaction ID
     * @param int $transId Transaction ID
     * @return Kp_Plugins_Authorizenet_Cim_Transaction_Refund
     */
    public function setTransId($transId) {
        $this->_transId = $transId;
        return $this;
    }

    /**
     * The last four digits of the credit card number to be refunded
     * @param string $creditCardNumberMasked
     * @return Kp_Plugins_Authorizenet_Cim_Transaction_Refund
     */
    public function setCreditCardNumberMasked($creditCardNumberMasked) {
        $this->_creditCardNumberMasked = $creditCardNumberMasked;
        return $this;
    }

    /**
     * The last four digits of the routing number to be refunded
     * @param string $bankRoutingNumberMasked
     * @return Kp_Plugins_Authorizenet_Cim_Transaction_Refund
     */
    public function setBankRoutingNumberMasked($bankRoutingNumberMasked) {
        $this->_bankRoutingNumberMasked = $bankRoutingNumberMasked;
        return $this;
    }

    /**
     * The last four digits of the bank account number to be refunded
     * @param string $bankAccountNumberMasked
     * @return Kp_Plugins_Authorizenet_Cim_Transaction_Refund
     */
    public function setBankAccountNumberMasked($bankAccountNumberMasked) {
        $this->_bankAccountNumberMasked = $bankAccountNumberMasked;
        return $this;
    }


}










