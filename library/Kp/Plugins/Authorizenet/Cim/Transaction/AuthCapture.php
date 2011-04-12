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
 * This is the most common type of credit card transaction and is the default
 * payment gateway transaction type. The amount is sent for authorization, and if
 * approved, is automatically submitted for settlement.
 *
 * @category   Zend
 * @package    Zend_Service
 * @subpackage AuthorizeNet
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
abstract class Kp_Plugins_Authorizenet_Cim_Transaction_AuthCapture extends Kp_Plugins_Authorizenet_Cim_Transaction_Abstract
{
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
        'order'                     => null,
        'taxExempt'                 => null,
        'recurringBilling'          => null,
        'cardCode'                  => null
    );

}










