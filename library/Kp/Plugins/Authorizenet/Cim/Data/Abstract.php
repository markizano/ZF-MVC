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
abstract class Kp_Plugins_Authorizenet
{
    /**
     * Expected consturctor arguments
     * @var array
     */
    protected $_expectedArgs = array();

    /**
     * Arguments that MUST be present
     * @var array
     */
    protected $_requiredArgs = array();

    /**
     * Kp_Plugins_Authorizenet_Cim::ADAPTER_XML | Kp_Plugins_Authorizenet_Cim::ADAPTER_SOAP
     * @var string
     */
    protected $_adapter = Kp_Plugins_Authorizenet_Cim::ADAPTER_XML;

    /**
     * Configuration is saved as Zend_Config for re-use in concrete classes
     * @var array
     */
    protected $_config = array();

    /**
     * Reads the configuration array an populates object attributes
     *
     * @param   mixed   $config     Instance of Zend_Config or plain array
     * @param   string  $adapter    Current adapter. Kp_Plugins_Authorizenet_Cim::ADAPTER_XML|ADAPTER_SOAP
     * @throws  Kp_Plugins_Authorizenet_Exception
     * @return  void
     */
    public function __construct($config = array(), $adapter = null)
    {
        // save current adapter - would be reused on object creation
        if(null != $adapter) {
            $this->_adapter = $adapter;
        }
        if(null == $this->_adapter) {
            throw new Kp_Plugins_Authorizenet_Exception('Invalid adapter. Must be one of Kp_Plugins_Authorizenet_Cim::ADAPTER_XML|ADAPTER_SOAP');
        }
        if ($config instanceof Zend_Config) {
            $config = $config->toArray();
        }

        if (!is_array($config)) {
            require_once 'Zend/Service/AuthorizeNet/Exception.php';
            throw new Kp_Plugins_Authorizenet_Exception('Adapter parameters must be in an array or a Zend_Config object');
        }

        // apply configuration (only if non-empty array is passed)
        if($config) {
            $this->setConfig($config);
        }
        
        // custom initialization (concrete classes override this method)
        $this->init();
    }

    /**
     * Allows concrete classes have their initialization block
     *
     * @return  void
     */
    public function init()
    {}

    /**
     * Configures current container
     *
     * @param   mixed   $config     Instance of Zend_Config or plain array
     * @throws  Kp_Plugins_Authorizenet_Exception
     * @return  void
     */
    public function setConfig($config)
    {
        $this->_config = $config;
        foreach($this->_expectedArgs as $name => $default) {
            if(isset($config[$name])) {
                if(!is_array($config[$name])) { // nested types are processed in concrete classes
                    $this->{'_' . $name} = $config[$name];
                }
            } else {
                if(null === $default) { // default is not provided - set as null
                    if(in_array($name, $this->_requiredArgs, true)) {
                        require_once 'Zend/Service/AuthorizeNet/Exception.php';
                        throw new Kp_Plugins_Authorizenet_Exception(get_class($this) . ': Required key not set - ' . $name);
                    }
                } else {
                    $this->{'_' . $name} = $default;
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
        foreach($this->_requiredArgs as $name) {
            if(!isset($this->{'_' . $name})) {
                require_once 'Zend/Service/AuthorizeNet/Exception.php';
                throw new Kp_Plugins_Authorizenet_Exception(get_class($this) . ': Required key not set - ' . $name);
            }
        }
    }

    /**
     * Returns XML representation of container
     *
     * @return  string
     */
    public function  __toString()
    {
        try {
            $output = $this->toString();
            return $output;
        } catch(Exception $e) {
            trigger_error($e->getMessage(), E_USER_WARNING);
            return '';
        }
    }
    
    /**
     * __toString cannot be used as rvalue at this time - so this hack
     *
     * @param   int $indent How many spaces to indent the content with
     * @throws Kp_Plugins_Authorizenet_Exception
     * @return  string
     */
    public function toString($indent = 0)
    {
        $this->validate(); // exception is thrown if validation is not passed
        
        $body = "";
        foreach($this->_expectedArgs as $name => $default) {
            $data = $this->{'_' . $name};
            if($data instanceof Kp_Plugins_Authorizenet_Cim_Data_Abstract) {
                $body .= $this->getTagValue($name, $data->toString($indent + 1), $indent, true);
            } elseif (is_array($data)) {
                foreach($data as $el) {
                    $body .= $this->getTagValue($name, $el->toString($indent + 1), $indent, true);
                }
            } else {
                $body .= $this->getTagValue($name, $data, $indent);
            }
        }

        return $body;
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
    protected function getTagValue($tag, $value, $indent = 0, $container = false)
    {
        $indent = str_repeat('    ', $indent);
        if(!(null === $value)) { // null values should be skipped
            if($container) {
                return $indent . "<{$tag}>\n{$value}" . $indent . "</{$tag}>\n";
            } else {
                return $indent . "<{$tag}>{$value}</{$tag}>\n";
            }
        } else {
            return '';
        }
    }
}




