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
 * Interface class for accessing CIM service
 *
 * @category   Zend
 * @package    Zend_Service
 * @subpackage AuthorizeNet
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Kp_Plugins_Authorizenet_Cim
{
    const ADAPTER_SOAP = 'soap';
    const ADAPTER_XML = 'xml';

    /**
     * Valid adapters
     * @var array
     */
    protected static $_validAdapters = array(
        self::ADAPTER_SOAP  => self::ADAPTER_SOAP,
        self::ADAPTER_XML   => self::ADAPTER_XML
    );

    /**
     * Currently selected adapter
     * @var string
     */
    protected static $_adapterType = null;
    
    /**
     * Factory method allowing to use different service adapters (XML or SOAP)
     *
     * @param   mixed   $config Either Zend_Config or array
     * @throws  Kp_Plugins_Authorizenet_Exception
     * @return  Kp_Plugins_Authorizenet_Cim_Adapter
     */
    public static function factory($config = array())
    {
        self::$_adapterType = isset($config['adapter']) ? $config['adapter'] : self::ADAPTER_XML;
        $loginId = isset($config['loginId']) ? $config['loginId'] : null;
        $tranKey = isset($config['tranKey']) ? $config['tranKey'] : null;
        $serviceUri = isset($config['serviceUri']) ? $config['serviceUri'] : null;
        $httpClient = isset($config['httpClient']) ? $config['httpClient'] : null;

        if(!in_array(self::$_adapterType, self::$_validAdapters, true)) {
            throw new Kp_Plugins_Authorizenet_Exception('Invalid adapter supplied');
        }
        
        $class = 'Kp_Plugins_Authorizenet_Cim_Adapter_' . ucfirst(self::$_adapterType);
        /*if (!class_exists($class)) {
            require_once 'Zend/Loader.php';
            Zend_Loader::loadClass($class);
        }*/

        $object = new $class($loginId, $tranKey, $serviceUri, $httpClient);

        /**
         * Verify created object
         */
        if(! $object instanceof Kp_Plugins_Authorizenet_ServizeNet_Cim_Adapter) {
            throw new Kp_Plugins_Authorizenet_Exception("Adapter class '$class' does not extend Kp_Plugins_Authorizenet_Cim_Adapter");
        }

        return $object;
    }

    /**
     * Setup default access keys
     *
     * @param   string  $loginId    API Login ID
     * @param   string  $tranKey    Transaction Key
     * @return  void
     */
    public static function setKeys($loginId, $tranKey)
    {
        Kp_Plugins_Authorizenet_Abstract::setKeys($loginId, $tranKey);
    }

    /**
     * Get currently selected adapter
     * @return string
     */
    public static function getAdapterType()
    {
        return self::$_adapterType;
    }
}




