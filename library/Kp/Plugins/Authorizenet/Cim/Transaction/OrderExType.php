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
 * AuthorzeNet schema - orderExType
 *
 * Contains information about the order
 *
 * @category   Zend
 * @package    Zend_Service
 * @subpackage AuthorizeNet
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Kp_Plugins_Authorizenet_Cim_Transaction_OrderExType extends Kp_Plugins_Authorizenet_Cim_Data_Abstract
{
    /**
     * The merchant assigned invoice number for the transaction.
     * Up to 20 characters (no symbols)
     * @var string
     */
    protected $_invoiceNumber = null;

    /**
     * The transaction description. Up to 255 characters (no symbols)
     * @var string
     */
    protected $_description = null;

    /**
     * The merchant assigned purchase order number. Up to 25 characters (no symbols)
     * @var string
     */
    protected $_purchaseOrderNumber = null;

    /**
     * List of expected arguments and their deafults (to be passed via $config in __construct())
     * @var array
     */
    protected $_expectedArgs = array(
        'invoiceNumber'         => null,
        'description'           => null,
        'purchaseOrderNumber'   => null,
    );
}