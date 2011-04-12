<?php
/**
 *  Kizano_Controller_Helper_Acl
 *
 *  LICENSE
 *
 *  This source file is subject to the new BSD license that is bundled
 *  with this package in the file LICENSE.txt.
 *  It is also available through the world-wide-web at this URL:
 *  http://framework.zend.com/license/new-bsd
 *  If you did not receive a copy of the license and are unable to
 *  obtain it through the world-wide-web, please send an email
 *  to license@zend.com so we can send you a copy immediately.
 *
 *  @category   Kizano
 *  @package    Controller
 *  @copyright  Copyright (c) 2009-2011 Markizano Draconus <markizano@markizano.net>
 *  @license    http://framework.zend.com/license/new-bsd     New BSD License
 *  @author     Markizano Draconus <markizano@markizano.net>
 */

/**
 *  Controller helper to aid in setting up the ACL and managing authentication.
 *
 *  @category   Kizano
 *  @package    Controller
 *  @copyright  Copyright (c) 2009-2011 Markizano Draconus <markizano@markizano.net>
 *  @license    http://framework.zend.com/license/new-bsd     New BSD License
 *  @author     Markizano Draconus <markizano@markizano.net>
 */
class Kizano_Controller_Helper_Acl extends Zend_Controller_Action_Helper_Abstract
{

    /**
     * Zend_Auth instance placeholder
     * 
     * @var Zend_Auth
     */
    protected $_auth;

    /**
     * Instance placeholder for the Kizano Acl
     * 
     * @var Kizano_Acl
     * 
     */
    protected $_acl;

    /**
     * Current user role
     * 
     * @var string
     */
    protected $_role;

    /**
     * Placeholder for the session
     * 
     * @var Zend_Session_Namespace
     */
    protected $_session;

    /**
     * Initializes the ACL controller helpser
     * 
     * @param acl    Zend_Acl    The instance of the ACL class.
     * 
     * @return void
     */
    public function __construct(Zend_Acl $acl)
    {
        $this->_acl = $acl;
        $this->_auth = Zend_Auth::getInstance();
        $this->_session = Zend_Registry::get('session');
    }

    /**
     * Hook into action controller preDispatch() workflow
     * 
     * @return void
     */
    public function preDispatch()
    {
        $valid = true;
        $request = $this->getRequest();
        $resource = $this->getResource();
        $privilege = $this->getPrivilege();
#var_dump(array('role' => $this->getRole(), 'resource' => $resource, 'privy' => $privilege, 'allowed' => $this->_acl->isAllowed($this->getRole(), $resource, $privilege)));die(__METHOD__);
        if (!$this->_acl->isAllowed($this->getRole(), $resource, $privilege)) {
            $this->_session->redirect = $_SERVER['REQUEST_URI'];
            Kizano_Strings::flash(LANG_LOGIN_PROHIBITED);
            $redir = Zend_Controller_Action_HelperBroker::getStaticHelper('Redirector');
            $redir->gotoSimple('login', 'user', 'user');
        }
    }

    /**
     * Gets the current user role based on their session
     * 
     * @return string
     */
    public function getRole()
    {
        $options = $this->_acl->getOptions();
        $auth = Zend_Auth::getInstance()->getIdentity();
        return Zend_Auth::getInstance()->hasIdentity()? $auth['type']: $options['default']['role'];
    }

    /**
     * Gets the current requested resource
     * 
     * @return string
     */
    public function getResource()
    {
        $request = $this->getRequest();
        $module = $request->getModuleName();
        $controller = $request->getControllerName();
        $action = $request->getActionName();
        $resource = $module . '_' . $controller . '_' . $action;
        return $resource;
    }

    /**
     * Gets the current requested privilege
     * 
     * @return string
     */
    public function getPrivilege()
    {
        $request = $this->getRequest();
        $action = $request->getActionName();
        return $action;
    }
}

