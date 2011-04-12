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
 * AuthorzeNet schema - lineItemType
 *
 * @category   Zend
 * @package    Zend_Service
 * @subpackage AuthorizeNet
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Kp_Plugins_Authorizenet_Cim_Transaction_LineItemType extends Kp_Plugins_Authorizenet_Cim_Data_Abstract
{
    /**
     * The ID assigned to the item. Up to 31 chars
     * @var string
     */
    protected $_itemId = null;

    /**
     * Item name. Up to 31 chars.
     * @var string
     */
    protected $_name = null;

    /**
     * Item description. Up to 255 chars.
     * @var string
     */
    protected $_description = null;

    /**
     * The quantity of an item. Up to 4 digits (up to two decimal places)
     * @var double
     */
    protected $_quantity = null;

    /**
     * Cost of an item per unit excluding tax, freight, and duty.
     * Up to 4 digits with a decimal point (no dollar symbol) Ex. 4.95
     * @var double
     */
    protected $_unitPrice = null;

    /**
     * Indicates whether the item is subject to tax
     * Standard Boolean logic, 0=FALSE and 1=TRUE
     * 
     * @var boolean
     */
    protected $_taxable = null;

    /**
     * List of expected arguments and their deafults (to be passed via $config in __construct())
     * @var array
     */
    protected $_expectedArgs = array(
        'itemId'        => null,
        'name'          => null,
        'description'   => null,
        'quantity'      => null,
        'unitPrice'     => null,
        'taxable'       => null,
    );

    /**
     * Arguments that MUST be present
     * @var array
     */
    protected $_requiredArgs = array(
        'itemId',
        'name',
        'quantity',
        'unitPrice',
    );

    /**
     * Init container
     * @return  void
     */
    public function init()
    {
        parent::init();
        // convert bool to int
        if(!(null === $this->_taxable)) {
            $this->_taxable = (int)$this->_taxable;
        }
    }

}