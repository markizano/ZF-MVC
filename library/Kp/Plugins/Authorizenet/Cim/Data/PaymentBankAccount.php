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
 * Contains bank account payment information for the payment profile
 * 
 * @category   Zend
 * @package    Zend_Service
 * @subpackage AuthorizeNet
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
abstract class Kp_Plugins_Authorizenet_Cim_Data_PaymentBankAccount extends Kp_Plugins_Authorizenet_Cim_Data_Abstract
{
    /**
     * The type of bank account for the payment profile: checking, savings, businessChecking
     * @var string
     */
    protected $_accountType = null;

    /**
     * Valid account types
     * @var array
     */
    protected static $_validAccountTypes = array(
        'checking', 'savings', 'businessChecking', null
    );

    /**
     * The routing number of the customer’s bank. 9 digits
     * @var int
     */
    protected $_routingNumber = null;

    /**
     * The customer’s bank account number. 5 to 17 digits
     * @var int
     */
    protected $_accountNumber = null;

    /**
     * The customer’s full name as listed on the bank account. Up to 22 characters
     * @var string
     */
    protected $_nameOnAccount = null;

    /**
     * The type of electronic check transaction. CCD, PPD, TEL, WEB
     * Currently, the CIM API does not support ARC or BOC transaction types.
     * @var string
     */
    protected $_echeckType = null;

    /**
     * Valid echeck types
     * @var array
     */
    protected static $_validEcheckTypes = array(
        'CCD', 'PPD', 'TEL', 'WEB', null
    );

    /**
     * The name of the bank associated with the bank account number. Up to 50 characters
     * @var string
     */
    protected $_bankName = null;

    /**
     * List of expected arguments and their deafults (to be passed via $config in __construct())
     * @var array
     */
    protected $_expectedArgs = array(
        'accountType'    => null,
        'routingNumber'   => null,
        'accountNumber'   => null,
        'nameOnAccount'   => null,
        'echeckType'   => null,
        'bankName'   => null,
    );

    /**
     * Arguments that MUST be present
     * @var array
     */
    protected $_requiredArgs = array(
        'routingNumber',
        'accountNumber',
        'nameOnAccount',
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
        if (!in_array($this->_accountType, self::$_validAccountTypes, true)) {
            require_once 'Zend/Service/AuthorizeNet/Exception.php';
            throw new Kp_Plugins_Authorizenet_Exception("Invalid account type. Valid options: " . implode(', ', self::$_validAccountTypes));
        }

        if(!in_array($this->_echeckType, self::$_validEcheckTypes, true)) {
            require_once 'Zend/Service/AuthorizeNet/Exception.php';
            throw new Kp_Plugins_Authorizenet_Exception("Invalid echeck type. Valid options: " . implode(', ', self::$_validEcheckTypes));
        }
    }

}




