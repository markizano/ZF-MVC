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
 * BillTo portion of payment profiles
 *
 * @category   Zend
 * @package    Zend_Service
 * @subpackage AuthorizeNet
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
abstract class Kp_Plugins_Authorizenet_Cim_Data_CustomerAddress extends Kp_Plugins_Authorizenet_Cim_Data_Abstract
{
    /**
     * The customer’s first name. Up to 50 characters (no symbols).
     * @var string
     */
    protected $_firstName = null;

    /**
     * The customer’s last name. Up to 50 characters (no symbols).
     * @var string
     */
    protected $_lastName = null;

    /**
     * The name of the company associated with the customer, if applicable.
     * Up to 50 characters (no symbols).
     * @var string
     */
    protected $_company = null;

    /**
     * The customer’s address. Up to 60 characters (no symbols).
     * @var string
     */
    protected $_address = null;

    /**
     * The city of the customer’s address. Up to 40 characters (no symbols).
     * @var string
     */
    protected $_city = null;

    /**
     * The state of the customer’s address. A valid two-character state code.
     * @var string
     */
    protected $_state = null;

    /**
     * The ZIP code of the customer’s address. Up to 20 characters (no symbols).
     * @var string
     */
    protected $_zip = null;

    /**
     * The country of the customer’s address. Up to 60 characters (no symbols).
     * @var string
     */
    protected $_country = null;

    /**
     * The phone number associated with the customer profile.
     * Up to 25 digits (no letters) Ex. (123)123-1234
     * @var string
     */
    protected $_phoneNumber = null;

    /**
     * The fax number associated with the customer profile.
     * Up to 25 digits (no letters) Ex. (123)123-1234
     * @var string
     */
    protected $_faxNumber = null;

    /**
     * Payment gateway assigned ID associated with the customer shipping address
     * Used on update ONLY
     * @var int
     */
    protected $_customerShippingAddressId = null;

    /**
     * List of expected arguments and their deafults (to be passed via $config in __construct())
     * @var array
     */
    protected $_expectedArgs = array(
        'firstName'     => null,
        'lastName'      => null,
        'company'       => null,
        'address'       => null,
        'city'          => null,
        'state'         => null,
        'zip'           => null,
        'country'       => null,
        'phoneNumber'   => null,
        'faxNumber'     => null,
        'customerShippingAddressId' => null
    );

    /**
     * The customer’s first name. Up to 50 characters (no symbols).
     * @param string $firstName
     * @return Kp_Plugins_Authorizenet_Cim_Data_CustomerAddress
     */
    public function setFirstName($firstName) {
        $this->_firstName = $firstName;
        return $this;
    }

    /**
     * The customer’s last name. Up to 50 characters (no symbols).
     * @param string $lastName
     * @return Kp_Plugins_Authorizenet_Cim_Data_CustomerAddress
     */
    public function setLastName($lastName) {
        $this->_lastName = $lastName;
        return $this;
    }

    /**
     * The name of the company associated with the customer, if applicable.
     * @param string $company
     * @return Kp_Plugins_Authorizenet_Cim_Data_CustomerAddress
     */
    public function setCompany($company) {
        $this->_company = $company;
        return $this;
    }

    /**
     * The customer’s address. Up to 60 characters (no symbols).
     * @param string $address
     * @return Kp_Plugins_Authorizenet_Cim_Data_CustomerAddress
     */
    public function setAddress($address) {
        $this->_address = $address;
        return $this;
    }

    /**
     * The city of the customer’s address. Up to 40 characters (no symbols).
     * @param string $city
     * @return Kp_Plugins_Authorizenet_Cim_Data_CustomerAddress
     */
    public function setCity($city) {
        $this->_city = $city;
        return $this;
    }

    /**
     * The state of the customer’s address. A valid two-character state code.
     * @param string $state
     * @return Kp_Plugins_Authorizenet_Cim_Data_CustomerAddress
     */
    public function setState($state) {
        $this->_state = $state;
        return $this;
    }

    /**
     * The ZIP code of the customer’s address. Up to 20 characters (no symbols).
     * @param string $zip
     * @return Kp_Plugins_Authorizenet_Cim_Data_CustomerAddress
     */
    public function setZip($zip) {
        $this->_zip = $zip;
        return $this;
    }

    /**
     * The country of the customer’s address. Up to 60 characters (no symbols).
     * @param string $country
     * @return Kp_Plugins_Authorizenet_Cim_Data_CustomerAddress
     */
    public function setCountry($country) {
        $this->_country = $country;
        return $this;
    }

    /**
     * The phone number associated with the customer profile.
     * @param string $phoneNumber
     * @return Kp_Plugins_Authorizenet_Cim_Data_CustomerAddress
     */
    public function setPhoneNumber($phoneNumber) {
        $this->_phoneNumber = $phoneNumber;
        return $this;
    }

    /**
     * The fax number associated with the customer profile.
     * @param string $faxNumber
     * @return Kp_Plugins_Authorizenet_Cim_Data_CustomerAddress
     */
    public function setFaxNumber($faxNumber) {
        $this->_faxNumber = $faxNumber;
        return $this;
    }

    public function setCustomerShippingAddressId($customerShippingAddressId) {
        $this->_customerShippingAddressId = $customerShippingAddressId;
        return $this;
    }


}




