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
 * Customer profile data container
 *
 * @category   Zend
 * @package    Zend_Service
 * @subpackage AuthorizeNet
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
abstract class Kp_Plugins_Authorizenet_Cim_Data_ProfileUpdate extends Kp_Plugins_Authorizenet_Cim_Data_Abstract
{
    /**
     * Payment gateway assigned ID associated with the customer profile
     * @var int
     */
    protected $_customerProfileId = null;

    /**
     * Merchant assigned ID for the customer. Up to 20 characters.
     * Required only if no values for both description and email are submitted.
     * @var string
     */
    protected $_merchantCustomerId = null;

    /**
     * Description of the customer or customer profile. Up to 255 characters.
     * Required only if no values for both merchantCustomerId and email are submitted.
     * @var string
     */
    protected $_description = null;

    /**
     * Email address associated with the customer profile. Up to 255 characters.
     * Required only if no values for both description and merchantCustomerId are submitted.
     * @var string
     */
    protected $_email = null;

    /**
     * List of expected arguments and their deafults (to be passed via $config in __construct())
     * @var array
     */
    protected $_expectedArgs = array(
        'merchantCustomerId'    => null,
        'description'           => null,
        'email'                 => null,
        'customerProfileId'     => null,
    );

    /**
     * Arguments that MUST be present
     * @var array
     */
    protected $_requiredArgs = array(
        'customerProfileId'
    );

}










