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
 * CIM data object creating factory
 * 
 * @category   Zend
 * @package    Zend_Service
 * @subpackage AuthorizeNet
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Kp_Plugins_Authorizenet_Cim_Data
{
    /**
     * Factory creating Kp_Plugins_Authorizenet_Cim_Data_* objects
     *
     * @param string    $container  Data container
     * @param mixed     $config     Container configuration options
     * @param string    $adapter    General adapter - Kp_Plugins_Authorizenet_Cim::ADAPTER_XML|ADAPTER_SOAP
     * @throws Kp_Plugins_Authorizenet_Exception
     * @return Kp_Plugins_Authorizenet_Cim_Data_Abstract
     */
    public static function factory($container, $config, $adapter = Kp_Plugins_Authorizenet_Cim::ADAPTER_XML)
    {
        $class = 'Kp_Plugins_Authorizenet_Cim_Data_' . ucfirst($adapter) . '_' . ucfirst($container);
        if (!class_exists($class)) {
            require_once 'Zend/Loader.php';
            Zend_Loader::loadClass($class);
        }
        $object = new $class($config, $adapter);
        
        /**
         * Verify created object
         */
        if(! $object instanceof Kp_Plugins_Authorizenet_Cim_Data_Abstract) {
            throw new Kp_Plugins_Authorizenet_Exception("Adapter class '$class' does not extend Kp_Plugins_Authorizenet_Cim_Data_Abstract");
        }

        return $object;
    }

    /**
     * Proxy method for Kp_Plugins_Authorizenet_Cim::getAdapterType()
     * Returns current adaptor or Kp_Plugins_Authorizenet_Cim::ADAPTER_XML if none is set yet.
     *
     * @param   string  If adapter is not null, it would be used, otherwise Kp_Plugins_Authorizenet_Cim::getAdapterType() is used
     * @return  string
     */
    private static function getAdapterType($adapter)
    {
        if(null == $adapter) { // rely on CIM wide adapter
            $adapter = Kp_Plugins_Authorizenet_Cim::getAdapterType();
            if(null == $adapter) {
                $adapter = Kp_Plugins_Authorizenet_Cim::ADAPTER_XML;
            }
        }
        return $adapter;
    }

    /**
     * Helper method, return factored Profile object
     *
     * @param mixed     $config     Container configuration options
     * @param string    $adapter    General adapter - Kp_Plugins_Authorizenet_Cim::ADAPTER_XML|ADAPTER_SOAP
     * @throws          Kp_Plugins_Authorizenet_Exception
     * @return          Kp_Plugins_Authorizenet_Cim_Data_Profile
     */
    public static function getProfile($config = array(), $adapter = null)
    {
        return self::factory('Profile', $config, self::getAdapterType($adapter));
    }

    /**
     * Helper method, return factored PaymentProfile object
     *
     * @param mixed     $config     Container configuration options
     * @param string    $adapter    General adapter - Kp_Plugins_Authorizenet_Cim::ADAPTER_XML|ADAPTER_SOAP
     * @throws Kp_Plugins_Authorizenet_Exception
     * @return  Kp_Plugins_Authorizenet_Cim_Data_PaymentProfile
     */
    public static function getPaymentProfile($config = array(), $adapter = null)
    {
        return self::factory('PaymentProfile', $config, self::getAdapterType($adapter));
    }

    /**
     * Helper method, return factored CustomerAddress object
     *
     * @param   mixed       $config     Container configuration options
     * @param   string      $adapter    General adapter - Kp_Plugins_Authorizenet_Cim::ADAPTER_XML|ADAPTER_SOAP
     * @throws  Kp_Plugins_Authorizenet_Exception
     * @return  Kp_Plugins_Authorizenet_Cim_Data_CustomerAddress
     */
    public static function getCustomerAddress($config = array(), $adapter = null)
    {
        return self::factory('customerAddress', $config, self::getAdapterType($adapter));

        // todo - I suppose it is save to use base type, so rest of the mehtod can be removed
        switch($type) {
            case 'billTo':
                return self::factory('billTo', $config, self::getAdapterType($adapter));
                break;
            case 'shipTo':
                return self::factory('shipTo', $config, self::getAdapterType($adapter));
                break;
            default:
                throw new Kp_Plugins_Authorizenet_Exception("Invalid address class '$type'");
                break;
        }
    }

    /**
     * Helper method, return factored PaymentCreditCard object
     *
     * @param mixed     $config     Container configuration options
     * @param string    $adapter    General adapter - Kp_Plugins_Authorizenet_Cim::ADAPTER_XML|ADAPTER_SOAP
     * @throws Kp_Plugins_Authorizenet_Exception
     * @return  Kp_Plugins_Authorizenet_Cim_Data_PaymentCreditCard
     */
    public static function getPaymentCreditCard($config = array(), $adapter = null)
    {
        return self::factory('PaymentCreditCard', $config, self::getAdapterType($adapter));
    }

    /**
     * Helper method, return factored PaymentBankAccount object
     *
     * @param mixed     $config     Container configuration options
     * @param string    $adapter    General adapter - Kp_Plugins_Authorizenet_Cim::ADAPTER_XML|ADAPTER_SOAP
     * @throws Kp_Plugins_Authorizenet_Exception
     * @return  Kp_Plugins_Authorizenet_Cim_Data_PaymentBankAccount
     */
    public static function getPaymentBankAccount($config = array(), $adapter = null)
    {
        return self::factory('PaymentBankAccount', $config, self::getAdapterType($adapter));
    }
}