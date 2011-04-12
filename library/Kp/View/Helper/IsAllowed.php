<?php

/** 
 * @see Zend_View_Helper_Abstract
 */
require_once 'Zend/View/Helper/Abstract.php';

/**
 * @see Zend_Registry
 */
require_once 'Zend/Registry.php';

/**
 * @see Zend_Controller_Action_HelperBroker
 */
require_once 'Zend/Controller/Action/HelperBroker.php';

/**
 * @see Zend_Auth
 */
require_once 'Zend/Auth.php';

/**
 * Allows you to easily ACL View Helpers
 * 
 * Example usage:
 *     <code>
 *         echo $this->isAllowed('admin/user', 'edit')
 *            ->partial('example/partials/test.phtml')
 *            ->ifNot()
 *            ->partial('example/partials/test2.phtml');
 *     </code>
 *     
 *     <code>
 *         if ($this->isAllowed('admin/user', 'edit')->allowed) {
 *             echo "Yaay! I'm Allowed";
 *         }
 *     </code>
 *
 * @category   Ic
 * @package    Kp_View
 * @subpackage Helper
 * @author     James Solomon <james@integraclick.com>
 */
class Kp_View_Helper_IsAllowed extends Zend_View_Helper_Abstract
{
    /**
     * Result of provided ACL check
     * 
     * @var boolean
     */
    private $_allowed = false;
    
    /**
     * @var Zend_Acl
     */
    protected $_acl;
    
    /**
     * @var Zend_Auth
     */
    protected $_auth;
    
    /**
     * Holds the last call that was made outside of this class
     * 
     * @var mixed
     */
    protected $_lastCall;
    
    /**
     * Holds current position in the sequence
     * 
     * @var string
     */
    protected $_sequence;
    
    /**
     * Holds all instances of this object
     * 
     * @var array
     */
    private static $_instances = array();
    
    /**
     * Initialize the class
     * 
     * @return void
     */
    public function __construct()
    {
        $this->_acl  = App_Acl::getInstance();
        $this->_auth = Zend_Auth::getInstance();
        
        // Load the current view, if one, and set it to our helper
        $viewRenderer = Zend_Controller_Action_HelperBroker::getStaticHelper('ViewRenderer');
        $this->setView($viewRenderer->view);
    }
    
    /**
     * The main helper function
     * 
     * Will intialize a new instance of the object and set the allowed variable.
     * Everytime this function is called a new instance is created and returned.
     * 
     * @param string $resource  ACL Resource to check
     * @param string $privilege ACL Privilege to check
     * @return Kp_View_Helper_IsAllowed
     */
    public function isAllowed($resource, $privilege)
    {
        $instance = self::_instance(true);
        $instance->_checkPrivilege($resource, $privilege);
        
        return $instance;
    }
    
    /**
     * Helps create new instances
     * 
     * @param boolean $new Optional: Will create a new instance
     * @return Kp_View_Helper_IsAllowed New instance or last created one
     */
    private static function _instance($new = false)
    {
        if ((bool) $new) {
            self::$_instances[] = new self();
        }
        
        return end(self::$_instances);
    }
    
    /**
     * If not allowed, then continue on
     * 
     * Basically, if the user is not allowed access to the provided 
     * resource and privilege, then clean the object, and return the
     * instance so we can chain a new view helper to do.
     * $resource
     * 
     * @return Kp_View_Helper_IsAllowed
     */
    public function ifNot()
    {
        $this->_allowed = !$this->_allowed;
        
        if ($this->_allowed) {
            $this->_lastCall = null;
        }
        
        return $this;
    }
    
    /**
     * Overload
     * 
     * Using it purely to deny access to the "allowed" variable
     * 
     * @param unknown_type $name
     * @param unknown_type $value
     */
    public function __get($name)
    {
        if ('allowed' == $name) {
            return $this->_allowed;
        }
    }
    
    /**
     * Overload
     * 
     * This will proxy calls to the view.  This way we can easily load
     * and call view helpers
     * 
     * @param string $method Unknown method called
     * @param array  $args   Arguments for the method
     * @return Kp_View_Helper_IsAllowed
     */
    public function __call($method, $args)
    {
        if ($this->_allowed) {
            
            /**
             * First try to proxy to the view helper that has been used,
             * else try to proxy to the view, to load the the helper
             */
            switch (true) {
                case is_callable(array($this->_lastCall, $method)):
                    $this->_lastCall = call_user_func_array(array($this->_lastCall, $method), $args);
                    break;
                    
                case is_callable(array($this->view, $method)):
                    $this->_lastCall = call_user_func_array(array($this->view, $method), $args);
                    break;
            }
        }
        
        return $this;
    }
    
    /**
     * Overload
     * 
     * This will see if there is a __toString to call in the proxied view
     * helper, or if it has returned a string.  If there is no string to return
     * then an empty on will be returned
     * 
     * @return string
     */
    public function __toString()
    {
        $string = $this->_lastCall;
        
        if ($this->_lastCall instanceof Zend_View_Helper_Abstract
            && is_callable(array($this->_lastCall, '__toString'))
            ) {
            $string = call_user_func_array(array($this->_lastCall, '__toString'), array());
        }
        
        return empty($string) || !is_string($string) ? '' : $string;
    }
    
    /**
     * Check the supplied resource and privilege for access
     * 
     * Will check the ACL for the current user, to see if they have access
     * to the supplied resource and privilege, and set the boolean of that
     * check in the public variable "allowed"
     * 
     * @param string $resource  ACL Resource to check
     * @param string $privilege ACL Privilege to check
     * @return boolean
     */
    protected function _checkPrivilege($resource, $privilege)
    {
        $this->_allowed = $this->_acl->isAllowed($resource, $privilege);
        return $this->_allowed;
    }
}