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
 * Contains bank credit card payment information for the payment profile
 *
 * @category   Zend
 * @package    Zend_Service
 * @subpackage AuthorizeNet
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
abstract class Kp_Plugins_Authorizenet_Cim_Data_PaymentCreditCard extends Kp_Plugins_Authorizenet_Cim_Data_Abstract
{
    /**
     * The customer's credit card number. 13 to 16 digits.
     * @var string
     */
    protected $_cardNumber = null;

    /**
     * The expiration date for the customer's credit card. YYYY-MM
     * @var string
     */
    protected $_expirationDate = null;

    /**
     * The three- or four-digit number on the back of a credit card (on the front for American Express)
     * @var string
     */
    protected $_cardCode = null;

    /**
     * List of expected arguments and their deafults (to be passed via $config in __construct())
     * @var array
     */
    protected $_expectedArgs = array(
        'cardNumber'    => null,
        'expirationDate'   => null,
        'cardCode'   => null
    );

    /**
     * Arguments that MUST be present
     * @var array
     */
    protected $_requiredArgs = array(
        'cardNumber',
        'expirationDate',
    );

    /**
     * The customer's credit card number. 13 to 16 digits.
     * @param string $cardNumber
     * @return Kp_Plugins_Authorizenet_Cim_Data_PaymentCreditCard
     */
    public function setCardNumber($cardNumber) {
        $this->_cardNumber = $cardNumber;
        return $this;
    }

    /**
     * The expiration date for the customer's credit card. YYYY-MM
     * @param string $expirationDate
     * @return Kp_Plugins_Authorizenet_Cim_Data_PaymentCreditCard
     */
    public function setExpirationDate($expirationDate) {
        $this->_expirationDate = $expirationDate;
        return $this;
    }

    /**
     * The three- or four-digit number on the back of a credit card (on the front for American Express)
     * @param string $cardCode
     * @return Kp_Plugins_Authorizenet_Cim_Data_PaymentCreditCard
     */
    public function setCardCode($cardCode) {
        $this->_cardCode = $cardCode;
        return $this;
    }
}




