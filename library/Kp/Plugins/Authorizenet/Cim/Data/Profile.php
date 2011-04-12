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
abstract class Kp_Plugins_AuthorizeNet_Cim_Data_Profile extends Kp_Plugins_Authorizenet_Cim_Data_Abstract
{
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
     * List of payment profiles - Kp_Plugins_Authorizenet_Data_PaymentProfiles
     * @var array
     */
    protected $_paymentProfiles = array();

    /**
     * Contains shipping address information for the customer profile
     * @var Kp_Plugins_Authorizenet_Cim_Data_CustomerAddress
     */
    protected $_shipToList = null;

    /**
     * List of expected arguments and their deafults (to be passed via $config in __construct())
     * @var array
     */
    protected $_expectedArgs = array(
        'merchantCustomerId'    => null,
        'description'           => null,
        'email'                 => null,
        'paymentProfiles'       => array(),
        'shipToList'            => null
    );

    /**
     * Inits customer profile container
     *
     * @throws  Kp_Plugins_Authorizenet_Exception
     * @return  void
     */
    public function init()
    {
        // nested types require special processing
        if(isset($this->_config['paymentProfiles']) && is_array($this->_config['paymentProfiles'])) {
            $paymentProfiles = $this->_config['paymentProfiles'];
            $this->_paymentProfiles = array();
        } else {
            $paymentProfiles = null;
        }


        if(isset($this->_config['shipToList'])) {
            $shipToList = $this->_config['shipToList'];
            $this->_shipToList = array();
        } else {
            $shipToList = null;
        }

        // set nested types
        if($paymentProfiles) {
            foreach($paymentProfiles as $paymentProfile) {
                if(is_array($paymentProfile)) {
                    $this->addPaymentProfileItem($paymentProfile);
                } else {
                    throw new Kp_Plugins_Authorizenet_Exception('PaymentProfile item must be enclosed in array');
                }
            }
        }

        if($shipToList) {
            foreach($shipToList as $shipToItem) {
                if(is_array($shipToItem)) {
                    $this->addShipToListItem($shipToItem);
                } else {
                    throw new Kp_Plugins_Authorizenet_Exception('ShipToList item must be enclosed in array');
                }
            }
        }
    }

    /**
     * Validates if container has all required fields set
     * 
     * @throws Kp_Plugins_Authorizenet_Exception
     * @return void
     */
    public function validate()
    {
        $cnt = (int)(null != $this->_merchantCustomerId)
             + (int)(null != $this->_description)
             + (int)(null != $this->_email) ;
        if($cnt < 1) {
            throw new Kp_Plugins_Authorizenet_Exception('No profile identificator provided');
        }
    }


    /**
     * Adds payment profile
     *
     * @param   mixed $paymentProfile   Payment profile as array or Kp_Plugins_Authorizenet_Cim_Data_PaymentProfile
     * @throws  Kp_Plugins_Authorizenet_Exception
     * @return  Kp_Plugins_Authorizenet_Cim_Data_Profile
     */
    public function addPaymentProfileItem($paymentProfile)
    {
        if($paymentProfile instanceof Kp_Plugins_Authorizenet_Cim_Data_PaymentProfile) {
            $this->_paymentProfiles[] = $paymentProfile;
        } else {
            $this->_paymentProfiles[] = Kp_Plugins_Authorizenet_Cim_Data::factory('PaymentProfile', $paymentProfile, $this->_adapter);
        }
        return $this;
    }

    /**
     * Sets ShipToList info
     *
     * @param   mixed $shipToList   Ship details as array or Kp_Plugins_Authorizenet_Cim_Data_ShipToList
     * @throws  Kp_Plugins_Authorizenet_Exception
     * @return  Kp_Plugins_Authorizenet_Cim_Data_Profile
     */
    public function addShipToListItem($shipToList)
    {
        if($shipToList instanceof Kp_Plugins_Authorizenet_Cim_Data_Xml_CustomerAddress) {
            $this->_shipToList[] = $shipToList;
        } else {
            $this->_shipToList[] = Kp_Plugins_Authorizenet_Cim_Data::factory('ShipToList', $shipToList, $this->_adapter);
        }
        return $this;
    }

    /**
     * Merchant assigned ID for the customer. Up to 20 characters.
     * Required only if no values for both description and email are submitted.
     * 
     * @param   string $merchantCustomerId
     * @return  Kp_Plugins_Authorizenet_Cim_Data_Profile
     */
    public function setMerchantCustomerId($merchantCustomerId) {
        $this->_merchantCustomerId = $merchantCustomerId;
        return $this;
    }

    /**
     * Description of the customer or customer profile. Up to 255 characters.
     * Required only if no values for both merchantCustomerId and email are submitted.
     *
     * @param   string $description
     * @return  Kp_Plugins_Authorizenet_Cim_Data_Profile
     */
    public function setDescription($description) {
        $this->_description = $description;
        return $this;
    }

    /**
     * Email address associated with the customer profile. Up to 255 characters.
     * Required only if no values for both description and merchantCustomerId are submitted.
     *
     * @param   string $email
     * @return  Kp_Plugins_Authorizenet_Cim_Data_Profile
     */
    public function setEmail($email) {
        $this->_email = $email;
        return $this;
    }

    /**
     * Merchant assigned ID for the customer
     * @return string
     */
    public function getMerchantCustomerId() {
        return $this->_merchantCustomerId;
    }

    /**
     * Description of the customer or customer profile
     * @return string
     */
    public function getDescription() {
        return $this->_description;
    }

    /**
     * Email address associated with the customer profile
     * @return string
     */
    public function getEmail() {
        return $this->_email;
    }
}










