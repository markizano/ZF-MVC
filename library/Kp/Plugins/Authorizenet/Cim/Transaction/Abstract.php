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
 * Base type for transactions classes
 *
 * @category   Zend
 * @package    Zend_Service
 * @subpackage AuthorizeNet
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
abstract class Kp_Plugins_Authorizenet_Cim_Transaction_Abstract extends Kp_Plugins_Authorizenet_Cim_Data_Abstract
{

    /**
     * The total amount of the transaction
     * @var double
     */
    protected $_amount = null;

    /**
     * Contains tax information for the transaction
     * @var Kp_Plugins_Authorizenet_Cim_Transaction_ExtendedAmountType
     */
    protected $_tax = null;

    /**
     * Contains shipping information for the transaction
     * @var Kp_Plugins_Authorizenet_Cim_Transaction_ExtendedAmountType
     */
    protected $_shipping = null;

    /**
     * Contains duty information for the transaction
     * @var Kp_Plugins_Authorizenet_Cim_Transaction_ExtendedAmountType
     */
    protected $_duty = null;

    /**
     * Contains line item details about the order
     * Up to 30 distinct instances of this element may be included per transaction
     * to describe items included in the order.
     * 
     * @var Kp_Plugins_Authorizenet_Cim_Transaction_LineItemType
     */
    protected $_lineItems = array();

    /**
     * Payment gateway assigned ID associated with the customer profile
     * @var int
     */
    protected $_customerProfileId = null;

    /**
     * Payment gateway assigned ID associated with the customer payment profile
     * @var int
     */
    protected $_customerPaymentProfileId = null;

    /**
     * Payment gateway assigned ID associated with the customer shipping address
     *
     * If customerShippingAddressId is not passed, shipping information will not
     * be included with the transaction.
     * 
     * @var int
     */
    protected $_customerShippingAddressId = null;

    /**
     * The tax exempt status
     * @var boolean
     */
    protected $_taxExempt = null;

    /**
     * The recurring billing status.
     * @var boolean
     */
    protected $_recurringBilling = null;

    /**
     * The customer’s card code (the three- or four-digit number on the back or
     * front of a credit card)
     *
     * Required only when the merchant would like to use the Card Code Verification
     * (CCV) filter.
     * 
     * @var int
     */
    protected $_cardCode = null;

    /**
     * Contains information about the order
     * @var Kp_Plugins_Authorizenet_Cim_Transaction_OrderExType
     */
    protected $_order = null;

    /**
     * @param   mixed   $config    Instance of Zend_Config or plain array
     * @param   string  $adapter    Current adapter. Kp_Plugins_Authorizenet_Cim::ADAPTER_XML|ADAPTER_SOAP
     * @throws  Kp_Plugins_Authorizenet_Exception
     * @return  void
     */
    public function __construct($config = array(), $adapter = null)
    {
        parent::__construct($config, $adapter); // read the config and populate attributes

        $ammountTypes = array('tax', 'duty', 'shipping', 'order');
        foreach($ammountTypes as $name) {
            $varName = '_' . $name;
            if(isset($this->_config[$name]) && !$this->{$varName} instanceof Kp_Plugins_Authorizenet_Cim_Transaction_ExtendedAmountType) {
                $this->{'_set' . ucfirst($name)}($this->_config[$name]);
            }
        }
        
        if(isset($this->_config['lineItems']) && is_array($this->_config['lineItems'])) {
            $lineItems = $this->_config['lineItems'];
            $this->_lineItems = array();
            foreach($lineItems as $lineItem) {
                if(!$lineItem instanceof Kp_Plugins_Authorizenet_Cim_Transaction_LineItemType) {
                    $this->_addLineItem($lineItem);
                }
            }
        }

        // convert bool into int
        $boolVals = array('taxExempt', 'recurringBilling');
        foreach($boolVals as $name) {
            $varName = '_' . $name;
            if(!(null === $this->{$varName})) {
                $this->{$varName} = (int)$this->{$varName};
            }
        }
    }

    /**
     * Sets tax options
     * @param   double  $amount         The tax amount for the transaction. Up to 4 digits after the decimal point (no dollar symbol)
     * @param   string  $name           The name of the tax for the transaction. Up to 31 characters
     * @param   string  $description    The tax description for the transaction. Up to 255 characters
     * @return  Kp_Plugins_Authorizenet_Cim_Transaction_Abstract
     */
    public function setTax($amount, $name = null, $description = null)
    {
        $this->_tax = new Kp_Plugins_Authorizenet_Cim_Transaction_ExtendedAmountType(
            array('amount' => $amount, 'name' => $name, 'description' => $description), $this->_adapter
        );
        return $this;
    }

    /**
     * @param   mixed   $config    Instance of Zend_Config or plain array
     * @return  Kp_Plugins_Authorizenet_Cim_Transaction_Abstract
     */
    protected function _setTax($config)
    {
        $amount = isset($config['amount']) ? $config['amount'] : null;
        $name = isset($config['name']) ? $config['name'] : null;
        $description = isset($config['description']) ? $config['description'] : null;
        return $this->setTax($amount, $name, $description);
    }

    /**
     * Contains duty information for the transaction
     * @param   double  $amount         The duty amount for the transaction. Up to 4 digits after the decimal point (no dollar symbol)
     * @param   string  $name           The name of the duty for the transaction. Up to 31 characters
     * @param   string  $description    The duty description for the transaction. Up to 255 characters
     * @return  Kp_Plugins_Authorizenet_Cim_Transaction_Abstract
     */
    public function setDuty($amount, $name = null, $description = null)
    {
        $this->_duty = new Kp_Plugins_Authorizenet_Cim_Transaction_ExtendedAmountType(
            array('amount' => $amount, 'name' => $name, 'description' => $description), $this->_adapter
        );
        return $this;
    }

    /**
     * @param   mixed   $config    Instance of Zend_Config or plain array
     * @return  Kp_Plugins_Authorizenet_Cim_Transaction_Abstract
     */
    protected function _setDuty($config)
    {
        $amount = isset($config['amount']) ? $config['amount'] : null;
        $name = isset($config['name']) ? $config['name'] : null;
        $description = isset($config['description']) ? $config['description'] : null;
        return $this->setDuty($amount, $name, $description);
    }

    /**
     * Contains shipping information for the transaction
     * @param   double  $amount         The shipping amount for the transaction. Up to 4 digits after the decimal point (no dollar symbol)
     * @param   string  $name           The name of the shipping for the transaction. Up to 31 characters
     * @param   string  $description    The shipping description for the transaction. Up to 255 characters
     * @return  Kp_Plugins_Authorizenet_Cim_Transaction_Abstract
     */
    public function setShipping($amount, $name = null, $description = null)
    {
        $this->_shipping = new Kp_Plugins_Authorizenet_Cim_Transaction_ExtendedAmountType(
            array('amount' => $amount, 'name' => $name, 'description' => $description), $this->_adapter
        );
        return $this;
    }

    /**
     * @param   mixed   $config    Instance of Zend_Config or plain array
     * @return  Kp_Plugins_Authorizenet_Cim_Transaction_Abstract
     */
    protected function _setShipping($config)
    {
        $amount = isset($config['amount']) ? $config['amount'] : null;
        $name = isset($config['name']) ? $config['name'] : null;
        $description = isset($config['description']) ? $config['description'] : null;
        return $this->setShipping($amount, $name, $description);
    }

    /**
     * Contains information about the order
     * @param string $invoiceNumber
     * @param string $purchaseOrderNumber
     * @param string $description
     *
     * @return  Kp_Plugins_Authorizenet_Cim_Transaction_Abstract
     */
    public function setOrder($invoiceNumber = null, $purchaseOrderNumber = null, $description = null)
    {
        $this->_order = new Kp_Plugins_Authorizenet_Cim_Transaction_OrderExType(
            array(
                'invoiceNumber'         => $invoiceNumber,
                'purchaseOrderNumber'   => $purchaseOrderNumber,
                'description'           => $description
            ),
            $this->_adapter
        );
        return $this;
    }

    /**
     * @param   mixed   $config    Instance of Zend_Config or plain array
     * @return  Kp_Plugins_Authorizenet_Cim_Transaction_Abstract
     */
    protected function _setOrder($config)
    {
        $invoiceNumber = isset($config['invoiceNumber']) ? $config['invoiceNumber'] : null;
        $purchaseOrderNumber = isset($config['purchaseOrderNumber']) ? $config['purchaseOrderNumber'] : null;
        $description = isset($config['description']) ? $config['description'] : null;
        return $this->setOrder($invoiceNumber, $purchaseOrderNumber, $description);
    }

    /**
     * Contains line item details about the order
     *
     * @param string    $itemId         The ID assigned to the item
     * @param string    $name           A short description of an item
     * @param double    $quantity       The quantity of an item. Up to 4 digits (up to two decimal places)
     * @param double    $unitPrice      Cost of an item per unit excluding tax, freight, and duty. Up to 4 digits with a decimal point (no dollar symbol)
     * @param string    $description    A detailed description of an item
     * @param boolean   $taxable        Indicates whether the item is subject to tax.
     *
     * @return  Kp_Plugins_Authorizenet_Cim_Transaction_Abstract
     */
    public function addLineItem($itemId, $name, $quantity, $unitPrice, $description = null, $taxable = null)
    {
        $this->_lineItems[] = new Kp_Plugins_Authorizenet_Cim_Transaction_LineItemType(
            array(
                'itemId'        => $itemId,
                'name'          => $name,
                'quantity'      => $quantity,
                'unitPrice'     => $unitPrice,
                'description'   => $description,
                'taxable'       => $taxable
            ),
            $this->_adapter
        );
        return $this;
    }

    /**
     * @param   mixed   $config    Instance of Zend_Config or plain array
     * @return  Kp_Plugins_Authorizenet_Cim_Transaction_Abstract
     */
    protected function _addLineItem($config)
    {
        $itemId = isset($config['itemId']) ? $config['itemId'] : null;
        $name = isset($config['name']) ? $config['name'] : null;
        $description = isset($config['description']) ? $config['description'] : null;
        $quantity = isset($config['quantity']) ? $config['quantity'] : null;
        $unitPrice = isset($config['unitPrice']) ? $config['unitPrice'] : null;
        $taxable = isset($config['taxable']) ? $config['taxable'] : null;

        return $this->addLineItem($itemId, $name, $quantity, $unitPrice, $description, $taxable);
    }

    /**
     * The total amount of the transaction
     * @param double $amount
     * @return Kp_Plugins_Authorizenet_Cim_Transaction_Abstract
     */
    public function setAmount($amount) {
        $this->_amount = $amount;
        return $this;
    }

    /**
     * Payment gateway assigned ID associated with the customer profile
     * @param int $customerProfileId
     * @return Kp_Plugins_Authorizenet_Cim_Transaction_Abstract
     */
    public function setCustomerProfileId($customerProfileId) {
        $this->_customerProfileId = $customerProfileId;
        return $this;
    }

    /**
     * Payment gateway assigned ID associated with the customer payment profile
     * @param int $customerPaymentProfileId
     * @return Kp_Plugins_Authorizenet_Cim_Transaction_Abstract
     */
    public function setCustomerPaymentProfileId($customerPaymentProfileId) {
        $this->_customerPaymentProfileId = $customerPaymentProfileId;
        return $this;
    }

    /**
     * Payment gateway assigned ID associated with the customer shipping address
     * @param int $customerShippingAddressId
     * @return Kp_Plugins_Authorizenet_Cim_Transaction_Abstract
     */
    public function setCustomerShippingAddressId($customerShippingAddressId) {
        $this->_customerShippingAddressId = $customerShippingAddressId;
        return $this;
    }

    /**
     * The tax exempt status
     * @param boolean $taxExempt
     * @return Kp_Plugins_Authorizenet_Cim_Transaction_Abstract
     */
    public function setTaxExempt($taxExempt) {
        $this->_taxExempt = (int)$taxExempt;
        return $this;
    }

    /**
     * The recurring billing status.
     * @param boolean $recurringBilling
     * @return Kp_Plugins_Authorizenet_Cim_Transaction_Abstract
     */
    public function setRecurringBilling($recurringBilling) {
        $this->_recurringBilling = (int)$recurringBilling;
        return $this;
    }

    /**
     *
     * @param string $cardCode
     * @return Kp_Plugins_Authorizenet_Cim_Transaction_Abstract
     */
    public function setCardCode($cardCode) {
        $this->_cardCode = $cardCode;
        return $this;
    }
}
