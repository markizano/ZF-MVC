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
 * @category   Zend
 * @package    Zend_Service
 * @subpackage AuthorizeNet
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Kp_Plugins_Authorizenet_Cim_Adapter_Xml extends Kp_Plugins_Authorizenet_Cim_Adapter
{
    const URI_SERVICE   = 'https://api.authorize.net/xml/v1/request.api';
    const URI_SANDBOX   = 'https://apitest.authorize.net/xml/v1/request.api';

    /**
     * Extends Kp_Plugins_Authorizenet_Cim_Abstract's constructor
     *
     * @param   string              $loginId    API Login ID
     * @param   string              $tranKey    Transaction Key
     * @param   string              $serviceUri Service URI
     * @param   Zend_Http_Client    $httpClient Service transport
     * @throws  Kp_Plugins_Authorizenet_Exception
     * @return  void
     */
    public function  __construct($loginId = null, $tranKey = null, $serviceUri = null, Zend_Http_Client $httpClient = null)
    {
        if(null == $serviceUri) { // if URI is not provided bundled one is used
            $serviceUri = self::URI_SERVICE;
        }
        parent::__construct($loginId, $tranKey, $serviceUri, $httpClient);
    }

    /**
     * Turns sandbox mode on/off
     *
     * @param   boolean $flag   True to turn sandbox on, false otherwise
     * @return  Kp_Plugins_Authorizenet_Cim_Adapter
     */
    public function sandboxMode($flag)
    {
        if($flag) {
            $this->setServiceUri(self::URI_SANDBOX);
        } else {
            $this->setServiceUri(self::URI_SERVICE);
        }
        return $this;
    }

    /**
     * Wraps API calls into method-named tags + adds <xml> tag
     *
     * @param   string  $method RPC method to call
     * @param   string  $body   The body content of RPC message
     * @return  string
     */
    protected function wrapMethodBody($method, $body)
    {
        return '<?xml version="1.0" encoding="utf-8"?>' . "\n"
              .'<' . $method . ' xmlns= "AnetApi/xml/v1/schema/AnetApiSchema.xsd">' . "\n"
              . $this->getAuthorizationBlock()
              . $body
              . "</{$method}>";
    }

    /**
     * Creates authorization portion of requests
     *
     * @return  string
     */
    protected function getAuthorizationBlock()
    {
        return "<merchantAuthentication><name>{$this->_loginId}</name>"
              ."<transactionKey>{$this->_tranKey}</transactionKey></merchantAuthentication>\n";
    }
    
    /**
     * Generates single tag and its value
     *
     * @param   string  $tag    Tag to wrap the content into
     * @param   string  $value  Tag content
     * @param   int     $indent How much spaces to put before content (helps w/ readability)
     * @param   boolean $container  Whether current element is container for other tags
     * @return  string
     */
    protected function getTagValue($tag, $value, $indent = 0, $container = false, $cdata = false)
    {
        $indent = str_repeat('    ', $indent);
        if($cdata) {
            $value = '<![CDATA[' . $value . ']]>';
        }
        if(!empty($value)) {
            if($container) {
                return $indent . "<{$tag}>\n{$value}" . $indent . "</{$tag}>\n";
            } else {
                return $indent . "<{$tag}>{$value}</{$tag}>\n";
            }
        } else {
            return '';
        }
    }

    /**
     * Executes method and returns response
     *
     * @return  Kp_Plugins_Authorizenet_Cim_Response
     */
    protected function execute($payload)
    {
        $response =  $this->_httpClient->resetParameters()
                                       ->setRawData($payload, 'text/xml')
                                       ->request('POST');
        return new Kp_Plugins_Authorizenet_Cim_Response_Xml($response);
    }

    /**
     * API METHODS
     */

    /**
     * Create a new customer profile along with any customer payment profiles and customer
     * shipping addresses for the customer profile.
     *
     * @param   Kp_Plugins_Authorizenet_Cim_Data_Profile  $profile    Customer profile
     * @param   string  $refId  Merchant-assigned reference ID for the request. Up to 20 characters
     * @param   string  $validationMode Indicates the processing mode for the request (none, testMode, liveMode)
     *
     * @return  Kp_Plugins_Authorizenet_Cim_Response
     */
    public function createCustomerProfileRequest(
        Kp_Plugins_Authorizenet_Cim_Data_Profile $profile, $refId = null, $validationMode = 'none')
    {
        $payload = $profile->toString();
        if($refId) {
            $payload = $this->getTagValue('refId', $refId) . $payload;
        }
        if($validationMode) {
            $this->checkValidationMode($validationMode);
            $payload .= $this->getTagValue('validationMode', $validationMode);
        }
        
        $payload = $this->wrapMethodBody('createCustomerProfileRequest', $payload);
        
        return $this->execute($payload);
    }

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
    public function createCustomerPaymentProfileRequest(
        $customerProfileId, Kp_Plugins_Authorizenet_Cim_Data_PaymentProfile $paymentProfile, $refId = null, $validationMode = 'none')
    {
        $payload = $paymentProfile->toString(1);
        // wrap into paymentProfile tag
        $payload = $this->getTagValue('paymentProfile', $payload, 0, true);

        // add customerProfileId tag
        $payload = $this->getTagValue('customerProfileId', $customerProfileId) . $payload;
        
        if($refId) {
            $payload = $this->getTagValue('refId', $refId) . $payload;
        }
        if($validationMode) {
            $this->checkValidationMode($validationMode);
            $payload .= $this->getTagValue('validationMode', $validationMode);
        }

        $payload = $this->wrapMethodBody('createCustomerPaymentProfileRequest', $payload);

        return $this->execute($payload);
    }

    /**
     * This function is used to create a new customer shipping address for an existing customer profile.
     *
     * @param   int     $customerProfileId  Customer profile ID. Payment profile would be binded to that customer
     * @param   Kp_Plugins_Authorizenet_Cim_Data_CustomerAddress   $address   Address data
     * @param   string  $refId              Merchant-assigned reference ID for the request. Up to 20 characters
     *
     * @return  Kp_Plugins_Authorizenet_Cim_Response
     */
    public function createCustomerShippingAddressRequest(
        $customerProfileId, Kp_Plugins_Authorizenet_Cim_Data_CustomerAddress $address, $refId = null)
    {
        $payload = $address->toString(1);
        // wrap into address tag
        $payload = $this->getTagValue('address', $payload, 0, true);

        // add customerProfileId tag
        $payload = $this->getTagValue('customerProfileId', $customerProfileId) . $payload;

        if($refId) {
            $payload = $this->getTagValue('refId', $refId) . $payload;
        }

        $payload = $this->wrapMethodBody('createCustomerShippingAddressRequest', $payload);

        return $this->execute($payload);
    }

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
    public function createCustomerProfileTransactionRequest(
        Kp_Plugins_Authorizenet_Cim_Transaction_Abstract $transaction, $extraOptions = null, $refId = null)
    {
        $payload = $transaction->toString(1);
        $payload = $this->getTagValue('transaction', $payload, null, true);

        if($refId) {
            $payload = $this->getTagValue('refId', $refId) . $payload;
        }

        if($extraOptions) {
            $payload .= $this->getTagValue('extraOptions', $extraOptions, null, 0, true);
        }

        $payload = $this->wrapMethodBody('createCustomerProfileTransactionRequest', $payload);

//        echo '<pre>';
//        echo str_replace('<', '&lt;', $payload);
//        exit;

        return $this->execute($payload);
    }

    /**
     * This function is used to delete an existing customer profile along with
     * all associated customer payment profiles and customer shipping addresses.
     *
     * @param int       $customerProfileId  Payment gateway assigned ID associated with the customer profile
     * @param string    $refId              Merchant-assigned reference ID for the request. Up to 20 characters.
     *
     * @return  Kp_Plugins_Authorizenet_Cim_Response
     */
    public function deleteCustomerProfileRequest($customerProfileId, $refId = null)
    {
        // add customerProfileId tag
        $payload = $this->getTagValue('customerProfileId', $customerProfileId);

        if($refId) {
            $payload = $this->getTagValue('refId', $refId) . $payload;
        }

        $payload = $this->wrapMethodBody('deleteCustomerProfileRequest', $payload);

        return $this->execute($payload);
    }

    /**
     * This function is used to delete a customer payment profile from an existing customer profile.
     *
     * @param int       $customerProfileId          Payment gateway assigned ID associated with the customer profile
     * @param int       $customerPaymentProfileId   Payment gateway assigned ID associated with the customer payment profile
     * @param string    $refId                      Merchant-assigned reference ID for the request. Up to 20 characters
     */
    public function deleteCustomerPaymentProfileRequest($customerProfileId, $customerPaymentProfileId, $refId = null)
    {
        // add customerProfileId and customerPaymentProfileId tags
        $payload = $this->getTagValue('customerProfileId', $customerProfileId);
        $payload .= $this->getTagValue('customerPaymentProfileId', $customerPaymentProfileId);

        if($refId) {
            $payload = $this->getTagValue('refId', $refId) . $payload;
        }

        $payload = $this->wrapMethodBody('deleteCustomerPaymentProfileRequest', $payload);

        return $this->execute($payload);
    }

    /**
     * This function is used to delete a customer shipping address from an existing customer profile.
     *
     * @param int       $customerProfileId          Payment gateway assigned ID associated with the customer profile
     * @param int       $customerShippingAddressId  Payment gateway assigned ID associated with the customer shipping address
     * @param string    $refId                      Merchant-assigned reference ID for the request. Up to 20 characters
     *
     * @return  Kp_Plugins_Authorizenet_Cim_Response
     */
    public function deleteCustomerShippingAddressRequest($customerProfileId, $customerShippingAddressId, $refId = null)
    {
        // add customerProfileId and customerShippingAddressId tags
        $payload = $this->getTagValue('customerProfileId', $customerProfileId);
        // inconsistent API on the part of ANet - instead of customerAddressId, customerShippingAddressId
        //should be expected. However, that's how they did it
        $payload .= $this->getTagValue('customerAddressId', $customerShippingAddressId);

        if($refId) {
            $payload = $this->getTagValue('refId', $refId) . $payload;
        }

        $payload = $this->wrapMethodBody('deleteCustomerShippingAddressRequest', $payload);

        return $this->execute($payload);
    }

    /**
     * This function is used to retrieve all existing customer profile Ids.
     * @return  Kp_Plugins_Authorizenet_Cim_Response
     */
    public function getCustomerProfileIdsRequest()
    {
        $payload = $this->wrapMethodBody('getCustomerProfileIdsRequest', '');

        return $this->execute($payload);
    }

    /**
     * This function is used to retrieve an existing customer profile along with
     * all the associated customer payment profiles and customer shipping addresses.
     *
     * @param   int     $customerProfileId  Customer profile ID. Payment profile would be binded to that customer
     *
     * @return  Kp_Plugins_Authorizenet_Cim_Response
     */
    public function getCustomerProfileRequest($customerProfileId)
    {
        // add customerProfileId tag
        $payload = $this->getTagValue('customerProfileId', $customerProfileId);

        $payload = $this->wrapMethodBody('getCustomerProfileRequest', $payload);

        return $this->execute($payload);
    }

    /**
     * This function is used to retrieve a customer payment profile for an existing customer profile.
     *
     * @param int $customerProfileId        Payment gateway assigned ID associated with the customer profile
     * @param int $customerPaymentProfileId Payment gateway assigned ID associated with the customer payment profile
     *
     * @return  Kp_Plugins_Authorizenet_Cim_Response
     */
    public function getCustomerPaymentProfileRequest($customerProfileId, $customerPaymentProfileId)
    {
        // add customerProfileId and customerPaymentProfileId tags
        $payload = $this->getTagValue('customerProfileId', $customerProfileId);
        $payload .= $this->getTagValue('customerPaymentProfileId', $customerPaymentProfileId);

        $payload = $this->wrapMethodBody('getCustomerPaymentProfileRequest', $payload);

        return $this->execute($payload);
    }

    /**
     * This function is used to retrieve a customer payment profile for an existing customer profile.
     *
     * @param int $customerProfileId            Payment gateway assigned ID associated with the customer profile
     * @param int $customerShippingAddressId    Payment gateway assigned ID associated with the customer shipping address
     *
     * @return  Kp_Plugins_Authorizenet_Cim_Response
     */
    public function getCustomerShippingAddressRequest($customerProfileId, $customerShippingAddressId)
    {
        // add customerProfileId and customerShippingAddressId tags
        $payload = $this->getTagValue('customerProfileId', $customerProfileId);
        // stupid incompatibility - element should be called customerShippingAddressId, but customerAddressId defined in schema
        $payload .= $this->getTagValue('customerAddressId', $customerShippingAddressId);

        $payload = $this->wrapMethodBody('getCustomerShippingAddressRequest', $payload);

        return $this->execute($payload);
    }

    /**
     * This function is used to update an existing customer profile.
     *
     * @param   int     $customerProfileId  Customer profile ID. Payment profile would be binded to that customer
     * @param Kp_Plugins_Authorizenet_Cim_Data_Profile $profile Contains payment information for the customer profile
     * @param string    $refId              Merchant-assigned reference ID for the request
     *
     * @return  Kp_Plugins_Authorizenet_Cim_Response
     */
    public function updateCustomerProfileRequest(
        $customerProfileId, Kp_Plugins_Authorizenet_Cim_Data_Profile $profile, $refId = null)
    {
        // create ProfileUpdate object
        $config = array(
            'customerProfileId'     => $customerProfileId,
            'merchantCustomerId'    => $profile->getMerchantCustomerId(),
            'description'           => $profile->getDescription(),
            'email'                 => $profile->getEmail(),
        );
        $profile = Kp_Plugins_Authorizenet_Cim_Data::factory('ProfileUpdate', $config, Kp_Plugins_Authorizenet_Cim::ADAPTER_XML);
        
        $payload = $profile->toString();

        if($refId) {
            $payload = $this->getTagValue('refId', $refId) . $payload;
        }

        $payload = $this->wrapMethodBody('updateCustomerProfileRequest', $payload);
        return $this->execute($payload);
    }

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
    public function updateCustomerPaymentProfileRequest(
        $customerProfileId, Kp_Plugins_Authorizenet_Cim_Data_PaymentProfile $paymentProfile, $refId = null, $validationMode = 'none')
    {
        $payload = $paymentProfile->toString(1);
        // wrap into paymentProfile tag
        $payload = $this->getTagValue('paymentProfile', $payload, 0, true);

        // add customerProfileId tag
        $payload = $this->getTagValue('customerProfileId', $customerProfileId) . $payload;

        if($refId) {
            $payload = $this->getTagValue('refId', $refId) . $payload;
        }
        if($validationMode) {
            $this->checkValidationMode($validationMode);
            $payload .= $this->getTagValue('validationMode', $validationMode);
        }

        $payload = $this->wrapMethodBody('updateCustomerPaymentProfileRequest', $payload);

        return $this->execute($payload);
    }

    /**
     * This function is used to update a shipping address for an existing customer profile.
     *
     * @param   int     $customerProfileId  Customer profile ID. Payment profile would be binded to that customer
     * @param   Kp_Plugins_Authorizenet_Cim_Data_CustomerAddress   $address   Address data
     * @param   string  $refId              Merchant-assigned reference ID for the request. Up to 20 characters
     *
     * @return  Kp_Plugins_Authorizenet_Cim_Response
     */
    public function updateCustomerShippingAddressRequest(
        $customerProfileId, Kp_Plugins_Authorizenet_Cim_Data_CustomerAddress $address, $refId = null)
    {
        $payload = $address->toString(1);

        // due to incompatibility introduced by ANet customerShippingAddressId should be replaced with customerAddressId
        // although prior should be used, ANet schema breaks its own convention (and this is not even shown in docs)
        $payload = str_replace('customerShippingAddressId>', 'customerAddressId>', $payload);

        // wrap into address tag
        $payload = $this->getTagValue('address', $payload, 0, true);

        // add customerProfileId tag
        $payload = $this->getTagValue('customerProfileId', $customerProfileId) . $payload;

        if($refId) {
            $payload = $this->getTagValue('refId', $refId) . $payload;
        }

        $payload = $this->wrapMethodBody('updateCustomerShippingAddressRequest', $payload);

        return $this->execute($payload);
    }

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
    public function validateCustomerPaymentProfileRequest(
        $customerProfileId, $customerPaymentProfileId, $customerShippingAddressId = null, $cardCode = null, $validationMode = 'testMode')
    {
        $payload = $this->getTagValue('customerProfileId', $customerProfileId);
        $payload .= $this->getTagValue('customerPaymentProfileId', $customerPaymentProfileId);

        if($customerShippingAddressId) {
            $payload .= $this->getTagValue('customerShippingAddressId', $customerShippingAddressId);
        }

        if($cardCode) {
            $payload .= $this->getTagValue('cardCode', $cardCode);
        }

        if($validationMode) {
            $this->checkValidationMode($validationMode, array('testMode', 'liveMode'));
            $payload .= $this->getTagValue('validationMode', $validationMode);
        }

        $payload = $this->wrapMethodBody('validateCustomerPaymentProfileRequest', $payload);

        return $this->execute($payload);
    }
}