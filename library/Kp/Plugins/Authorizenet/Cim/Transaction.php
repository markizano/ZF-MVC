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
 * Transaction factory
 *
 * @category   Zend
 * @package    Zend_Service
 * @subpackage AuthorizeNet
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Kp_Plugins_Authorizenet_Cim_Transaction
{

    const AUTH_CAPTURE          = 'AuthCapture';
    const AUTH_ONLY             = 'AuthOnly';
    const PRIOR_AUTH_CAPTURE    = 'PriorAuthCapture';
    const CAPTURE_ONLY          = 'CaptureOnly';
    const CREDIT                = 'Refund';
    const VOID                  = 'Void';

    /**
     * Valid transaction types
     * @var array
     */
    protected static $_validTypes = array(
        self::AUTH_CAPTURE,
        self::AUTH_ONLY,
        self::PRIOR_AUTH_CAPTURE,
        self::CAPTURE_ONLY,
        self::CREDIT,
        self::VOID
    );

    /**
     * Factory creating Kp_Plugins_Authorizenet_Cim_Transaction_* objects
     *
     * @param string    $container  Data container
     * @param mixed     $config     Container configuration options. self::AUTH_CAPTURE|AUTH_ONLY etc
     * @param string    $adapter    General adapter - Kp_Plugins_Authorizenet_Cim::ADAPTER_XML|ADAPTER_SOAP
     * @throws Kp_Plugins_Authorizenet_Exception
     * 
     * @return Kp_Plugins_Authorizenet_Cim_Transaction_Abstract
     */
    public static function factory($container, $config, $adapter = Kp_Plugins_Authorizenet_Cim::ADAPTER_XML)
    {
        if(!in_array($container, self::$_validTypes, true)) {
            throw new Kp_Plugins_Authorizenet_Exception('Invalid transaction type. Valid types: ' . implode(', ', self::$_validTypes));
        }

        $class = 'Kp_Plugins_Authorizenet_Cim_Transaction_' . ucfirst($adapter) . '_' . ucfirst($container);
        if (!class_exists($class)) {
            require_once 'Zend/Loader.php';
            Zend_Loader::loadClass($class);
        }
        $object = new $class($config, $adapter);

        /**
         * Verify created object
         */
        if(! $object instanceof Kp_Plugins_Authorizenet_Cim_Data_Abstract) {
            throw new Kp_Plugins_Authorizenet_Exception("Adapter class '$class' does not extend Kp_Plugins_Authorizenet_Cim_Transaction_Abstract");
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
     * Helper method, return factored AuthCapture object
     *
     * @param mixed     $config     Container configuration transaction options
     * @param string    $adapter    General adapter - Kp_Plugins_Authorizenet_Cim::ADAPTER_XML|ADAPTER_SOAP
     * @throws          Kp_Plugins_Authorizenet_Exception
     * @return          Kp_Plugins_Authorizenet_Cim_Transaction_AuthCapture
     */
    public static function getAuthCapture($config = array(), $adapter = null)
    {
        return self::factory('AuthCapture', $config, self::getAdapterType($adapter));
    }

    /**
     * Helper method, return factored AuthOnly object
     *
     * @param mixed     $config     Container configuration transaction options
     * @param string    $adapter    General adapter - Kp_Plugins_Authorizenet_Cim::ADAPTER_XML|ADAPTER_SOAP
     * @throws          Kp_Plugins_Authorizenet_Exception
     * @return          Kp_Plugins_Authorizenet_Cim_Transaction_AuthOnly
     */
    public static function getAuthOnly($config = array(), $adapter = null)
    {
        return self::factory('AuthOnly', $config, self::getAdapterType($adapter));
    }

    /**
     * Helper method, return factored PriorAuthCapture object
     *
     * @param mixed     $config     Container configuration transaction options
     * @param string    $adapter    General adapter - Kp_Plugins_Authorizenet_Cim::ADAPTER_XML|ADAPTER_SOAP
     * @throws          Kp_Plugins_Authorizenet_Exception
     * @return          Kp_Plugins_Authorizenet_Cim_Transaction_PriorAuthCapture
     */
    public static function getPriorAuthCapture($config = array(), $adapter = null)
    {
        return self::factory('PriorAuthCapture', $config, self::getAdapterType($adapter));
    }

    /**
     * Helper method, return factored CaptureOnly object
     *
     * @param mixed     $config     Container configuration transaction options
     * @param string    $adapter    General adapter - Kp_Plugins_Authorizenet_Cim::ADAPTER_XML|ADAPTER_SOAP
     * @throws          Kp_Plugins_Authorizenet_Exception
     * @return          Kp_Plugins_Authorizenet_Cim_Transaction_CaptureOnly
     */
    public static function getCaptureOnly($config = array(), $adapter = null)
    {
        return self::factory('CaptureOnly', $config, self::getAdapterType($adapter));
    }

    /**
     * Helper method, return factored Refund object
     *
     * @param mixed     $config     Container configuration transaction options
     * @param string    $adapter    General adapter - Kp_Plugins_Authorizenet_Cim::ADAPTER_XML|ADAPTER_SOAP
     * @throws          Kp_Plugins_Authorizenet_Exception
     * @return          Kp_Plugins_Authorizenet_Cim_Transaction_Refund
     */
    public static function getRefund($config = array(), $adapter = null)
    {
        return self::factory('Refund', $config, self::getAdapterType($adapter));
    }

    /**
     * Helper method, return factored Void transaction object
     *
     * @param mixed     $config     Container configuration options
     * @param string    $adapter    General adapter - Kp_Plugins_Authorizenet_Cim::ADAPTER_XML|ADAPTER_SOAP
     * @throws          Kp_Plugins_Authorizenet_Exception
     * @return          Kp_Plugins_Authorizenet_Cim_Transaction_Void
     */
    public static function getVoid($config = array(), $adapter = null)
    {
        return self::factory('Void', $config, self::getAdapterType($adapter));
    }
}