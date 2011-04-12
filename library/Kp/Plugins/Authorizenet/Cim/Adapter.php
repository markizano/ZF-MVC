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
 * Basic CIM Service Adapter
 *
 * @category   Zend
 * @package    Zend_Service
 * @subpackage AuthorizeNet
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
abstract class Kp_Plugins_Authorizenet_Cim_Adapter extends Kp_Plugins_Authorizenet_Abstract
{
    /**
     * Indicates the processing mode for the request. none, testMode, liveMode
     * In Test Mode, only field validation is performed. In Live Mode, a
     * transaction is generated and submitted to the processor with the amount
     * of $0.01. If successful, the transaction is immediately voided. When a
     * value of "none" is submitted, no additional validation is performed.
     * @var string
     */
    protected static $_validValidationModes = array(
        'none', 'testMode', 'liveMode', null
    );

    /**
     * Executes method and returns responce
     *
     * @return  Kp_Plugins_Authorizenet_Cim_Response
     */
    abstract protected function execute($payload);
    
    /**
     * Turns sandbox mode on/off
     *
     * @param   boolean $flag   True to turn sandbox on, false otherwise
     * @return  Kp_Plugins_Authorizenet_Cim_Adapter
     */
    abstract public function sandboxMode($flag);
    
    /**
     * Checks validation mode values
     *
     * @param   string  $mode   Validation mode. One of 'none', 'testMode', 'liveMode', null
     * @param   array   $modes  Array of modes to check agains. So that custom checks are possible
     * @return  void
     */
    protected function checkValidationMode($mode, $modes = null)
    {
        $modes = $modes ? $modes : self::$_validValidationModes;
        if(!in_array($mode, $modes, true)) {
            throw new Kp_Plugins_Authorizenet_Exception("Invalid validation mode '{$mode}'. Valid moded: " . implode(', ', $modes));
        }
    }

    
    /**
     * API METHODS
     */
    
    /**
     * Create a new customer profile along with any customer payment profiles and customer
     * shipping addresses for the customer profile.
     *
     * @param   Kp_Plugins_Authorizenet_Cim_Data_Profile  $profile    Customer profile
     * @param   string  $refId          Merchant-assigned reference ID for the request. Up to 20 characters
     * @param   string  $validationMode Indicates the processing mode for the request (none, testMode, liveMode)
     *
     * @return  Kp_Plugins_Authorizenet_Cim_Response
     */
    abstract public function createCustomerProfileRequest(
        Kp_Plugins_AuthorizeNet_Cim_Data_Profile $profile, $refId = null, $validationMode = 'none');

    /**
     * This function is used to create a new customer payment profile for an existing customer profile.
     *
     * @param   int     $customerProfileId  Customer profile ID. Payment profile would be binded to that customer
     * @param   Kp_Plugins_Authorizenet_Cim_Data_PaymentProfile   $paymentProfile Payment profile data
     * @param   string  $refId              Merchant-assigned reference ID for the request. Up to 20 characters
     * @param   string  $validationMode     Indicates the processing mode for the request (none, testMode, liveMode)
     *
     * @return  Kp_Plugins_Authorizenet_Cim_Response
     */
    abstract public function createCustomerPaymentProfileRequest(
        $customerProfileId, Kp_Plugins_Authorizenet_Cim_Data_PaymentProfile $paymentProfile, $refId = null, $validationMode = 'none');

    /**
     * This function is used to create a new customer shipping address for an existing customer profile.
     *
     * @param   int     $customerProfileId  Customer profile ID. Payment profile would be binded to that customer
     * @param   Kp_Plugins_Authorizenet_Cim_Data_CustomerAddress   $address   Address data
     * @param   string  $refId              Merchant-assigned reference ID for the request. Up to 20 characters
     *
     * @return  Kp_Plugins_Authorizenet_Cim_Response
     */
    abstract public function createCustomerShippingAddressRequest(
        $customerProfileId, Kp_Plugins_Authorizenet_Cim_Data_CustomerAddress $address, $refId = null);
    
    /**
     * This function is used to create a payment transaction from an existing customer profile.
     * You can submit one of six transaction types: Authorization Only, Authorization and Capture,
     * Capture Only, Prior Authorization and Capture, Refund and Void.
     * 
     * @param   Kp_Plugins_Authorizenet_Cim_Transaction_Abstract  $transaction Contains transaction information
     * @param   string  $extraOptions   Information in name/value pair format that does not exist within CIM
     * @param   string  $refId          Merchant-assigned reference ID for the request. Up to 20 characters
     *
     * @return  Kp_Plugins_Authorizenet_Cim_Response
     */
    abstract public function createCustomerProfileTransactionRequest(
        Kp_Plugins_Authorizenet_Cim_Transaction_Abstract $transaction, $extraOptions = null, $refId = null);
    
    /**
     * This function is used to delete an existing customer profile along with
     * all associated customer payment profiles and customer shipping addresses.
     *
     * @param int       $customerProfileId  Payment gateway assigned ID associated with the customer profile
     * @param string    $refId              Merchant-assigned reference ID for the request. Up to 20 characters.
     *
     * @return  Kp_Plugins_Authorizenet_Cim_Response
     */
    abstract public function deleteCustomerProfileRequest($customerProfileId, $refId = null);

    /**
     * This function is used to delete a customer payment profile from an existing customer profile.
     * 
     * @param int       $customerProfileId          Payment gateway assigned ID associated with the customer profile
     * @param int       $customerPaymentProfileId   Payment gateway assigned ID associated with the customer payment profile
     * @param string    $refId                      Merchant-assigned reference ID for the request. Up to 20 characters
     * 
     * @return  Kp_Plugins_Authorizenet_Cim_Response
     */
    abstract public function deleteCustomerPaymentProfileRequest($customerProfileId, $customerPaymentProfileId, $refId = null);

    /**
     * This function is used to delete a customer shipping address from an existing customer profile.
     * 
     * @param int       $customerProfileId          Payment gateway assigned ID associated with the customer profile
     * @param int       $customerShippingAddressId  Payment gateway assigned ID associated with the customer shipping address
     * @param string    $refId                      Merchant-assigned reference ID for the request. Up to 20 characters
     *
     * @return  Kp_Plugins_Authorizenet_Cim_Response
     */
    abstract public function deleteCustomerShippingAddressRequest($customerProfileId, $customerShippingAddressId, $refId = null);

    /**
     * This function is used to retrieve all existing customer profile Ids.
     * 
     * @return  Kp_Plugins_Authorizenet_Cim_Response
     */
    abstract public function getCustomerProfileIdsRequest();

    /**
     * This function is used to retrieve an existing customer profile along with
     * all the associated customer payment profiles and customer shipping addresses.
     *
     * @param   int     $customerProfileId  Customer profile ID. Payment profile would be binded to that customer
     *
     * @return  Kp_Plugins_Authorizenet_Cim_Response
     */
    abstract public function getCustomerProfileRequest($customerProfileId);

    /**
     * This function is used to retrieve a customer payment profile for an existing customer profile.
     *
     * @param int $customerProfileId        Payment gateway assigned ID associated with the customer profile
     * @param int $customerPaymentProfileId Payment gateway assigned ID associated with the customer payment profile
     * 
     * @return  Kp_Plugins_Authorizenet_Cim_Response
     */
    abstract public function getCustomerPaymentProfileRequest($customerProfileId, $customerPaymentProfileId);

    /**
     * This function is used to retrieve a customer payment profile for an existing customer profile.
     *
     * @param int $customerProfileId            Payment gateway assigned ID associated with the customer profile
     * @param int $customerShippingAddressId    Payment gateway assigned ID associated with the customer shipping address
     *
     * @return  Kp_Plugins_Authorizenet_Cim_Response
     */
    abstract public function getCustomerShippingAddressRequest($customerProfileId, $customerShippingAddressId);

    /**
     * This function is used to update an existing customer profile.
     *
     * @param   int     $customerProfileId  Customer profile ID. Payment profile would be binded to that customer
     * @param Kp_Plugins_Authorizenet_Cim_Data_Profile $profile Contains payment information for the customer profile
     * @param string    $refId              Merchant-assigned reference ID for the request
     * 
     * @return  Kp_Plugins_Authorizenet_Cim_Response
     */
    abstract public function updateCustomerProfileRequest(
        $customerProfileId, Kp_Plugins_Authorizenet_Cim_Data_Profile $profile, $refId = null);

    /**
     * This function is used to update a customer payment profile for an existing customer profile.
     *
     * If some elements in this request are not submitted or are submitted with 
     * a blank value, the values in the original profile will be removed. As a 
     * best practice to prevent this from happening, before calling 
     * updateCustomerPaymentProfileRequest, call getCustomerPaymentProfileRequest. 
     * That will return all current information including masked payment information. 
     * Then simply change the field that needs updating and use that data to call 
     * updateCustomerPaymentProfileRequest.
     *
     * @param   int     $customerProfileId  Customer profile ID. Payment profile would be binded to that customer
     * @param   Kp_Plugins_Authorizenet_Cim_Data_PaymentProfile   $paymentProfile Payment profile data
     * @param   string  $refId              Merchant-assigned reference ID for the request. Up to 20 characters
     * @param   string  $validationMode     Indicates the processing mode for the request (none, testMode, liveMode)
     *
     * @return  Kp_Plugins_Authorizenet_Cim_Response
     */
    abstract public function updateCustomerPaymentProfileRequest(
        $customerProfileId, Kp_Plugins_Authorizenet_Cim_Data_PaymentProfile $paymentProfile, $refId = null, $validationMode = 'none');
    
    /**
     * This function is used to update a shipping address for an existing customer profile.
     * 
     * @param   int     $customerProfileId  Customer profile ID. Payment profile would be binded to that customer
     * @param   Kp_Plugins_Authorizenet_Cim_Data_CustomerAddress   $address   Address data
     * @param   string  $refId              Merchant-assigned reference ID for the request. Up to 20 characters
     *
     * @return  Kp_Plugins_Authorizenet_Cim_Response
     */
    abstract public function updateCustomerShippingAddressRequest(
        $customerProfileId, Kp_Plugins_Authorizenet_Cim_Data_CustomerAddress $address, $refId = null);

    /**
     * This function is used to verify an existing customer payment profile by
     * generating a test transaction. No customer receipt emails are sent when
     * calling validateCustomerPaymentProfileRequest.
     *
     * @param int $customerProfileId            Payment gateway assigned ID associated with the customer profile
     * @param int $customerPaymentProfileId     Payment gateway assigned ID associated with the customer payment profile
     * @param int $customerShippingAddressId    OPTIONAL; Payment gateway assigned ID associated with the customer shipping address
     * @param int $cardCode                     OPTIONAL; The three- or four-digit number on the back of a credit card (on the front for American Express)
     * @param string $validationMode            One of testMode | liveMode
     * 
     * @return  Kp_Plugins_Authorizenet_Cim_Response
     */
    abstract public function validateCustomerPaymentProfileRequest(
        $customerProfileId, $customerPaymentProfileId, $customerShippingAddressId = null, $cardCode = null, $validationMode = 'testMode');

}
