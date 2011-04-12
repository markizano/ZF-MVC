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
 * @category   Ic
 * @package    Plugins
 * @subpackage Authorizenet
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */


/**
 * Base AuthorizeNet service class. Handles all necessary payment gateway
 * account access data.
 * 
 * @category   Ic
 * @package    Plugins
 * @subpackage Authorizenet
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
abstract class Kp_Plugins_Authorizenet_Abstract
{
    /**
     * @var string  AuthorizeNet API Login ID
     */
    protected static $_defaultLoginId = null;

    /**
     * @var string  AuthorizeNet Transaction Key
     */
    protected static $_defaultTranKey = null;

    /**
     * @var string  AuthorizeNet API Login ID
     */
    protected $_loginId = null;

    /**
     * @var string  AuthorizeNet Transaction Key
     */
    protected $_tranKey = null;

    /**
     * AuthorizeNet service end-point URI
     * @var Zend_Uri_Http
     */
    protected $_serviceUri = null;

    /**
     * Service transport
     * @var Zend_Http_Client
     */
    protected $_httpClient = null;

    /**
     * Setup default access keys
     *
     * @param   string  $loginId    API Login ID
     * @param   string  $tranKey    Transaction Key
     * @return  void
     */
    public static function setKeys($loginId, $tranKey)
    {
        self::$_defaultLoginId = $loginId;
        self::$_defaultTranKey = $tranKey;
    }

    /**
     * Basic constructor for descendants.
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
        $loginId = $loginId ? $loginId : self::$_defaultLoginId;
        $tranKey = $tranKey ? $tranKey : self::$_defaultTranKey;

        if(!$loginId || !$tranKey) {
            require_once 'Zend/Service/AuthorizeNet/Exception.php';
            throw new Kp_Plugins_Authorizenet_Exception('AuthorizeNet account access keys are missing');
        }

        $this->setHttpClient($httpClient);

        // exception would be thrown if invalid URI is provided
        $this->setServiceUri($serviceUri);

        $this->_loginId = $loginId;
        $this->_tranKey = $tranKey;
    }
    
    public function setHttpClient(Zend_Http_Client $httpClient = null)
    {
        if(null == $httpClient) { // setup default HTTP Client
            $this->_httpClient = new Zend_Http_Client();
            $this->_httpClient->setConfig(array(
                'adapter'      => 'Zend_Http_Client_Adapter_Socket',
                'maxredirects' => 0,
                'timeout'      => 60,
                'ssltransport' => 'ssl'
            ));
        } else {
            $this->_httpClient = $httpClient;
        }
        
        return $this;
    }

    /**
     * Returns API Login ID
     * @return string
     */
    protected function getLoginId() {
        return $this->_loginId;
    }
    
    /**
     * Returns Transaction Key
     * @return string
     */
    protected function getTranKey() {
        return $this->_tranKey;
    }
    
    /**
     * Sets service URI
     *
     * @param   string|Zend_Uri_Http  $serviceUri Respective service URI
     * @return  void
     */
    protected function setServiceUri($serviceUri)
    {
        if(!($serviceUri instanceof Zend_Uri_Http)) {
            $serviceUri = Zend_Uri_Http::factory($serviceUri);
        }
        if(!$serviceUri->valid()) {
            require_once 'Zend/Service/AuthorizeNet/Exception.php';
            throw new Kp_Plugins_Authorizenet_Exception('Invalid service URI');
        }
        $this->_serviceUri = $serviceUri;

        // update HTTP Client's URI
        $this->_httpClient->setUri($this->getServiceUri());

        return $this;
    }

    /**
     * Returns service URI
     *
     * @return  Zend_Uri_Http
     */
    protected function getServiceUri()
    {
        return $this->_serviceUri;
    }

}
