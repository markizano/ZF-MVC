<?php
/**
 *  Kizano_Auth_Adapter_Login
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
 *  @package    Auth
 *  @copyright  Copyright (c) 2009-2011 Markizano Draconus <markizano@markizano.net>
 *  @license    http://framework.zend.com/license/new-bsd     New BSD License
 *  @author     Markizano Draconus <markizano@markizano.net>
 */

/**
 *  Authentication adapter for logging in a user.
 *
 *  @category   Kizano
 *  @package    Auth
 *  @copyright  Copyright (c) 2009-2011 Markizano Draconus <markizano@markizano.net>
 *  @license    http://framework.zend.com/license/new-bsd     New BSD License
 *  @author     Markizano Draconus <markizano@markizano.net>
 */
class Kizano_Auth_Adapter_Login implements Zend_Auth_Adapter_Interface
{

    /**
     * The HTTP Request object
     *  
     * @var Zend_Controller_Request_Http
     */
    protected $_request;

    /**
     * The HTTP Response object
     *  
     * @var Zend_Controller_Response_Http
     */
    protected $_response;

    /**
     * Holds login information to be grabbed later in the application
     *  
     * @var Array
     */
    protected $_login;

    /**
     *  Bootstraps this class.
     *  
     *  @return void
     */
    public function __construct()
    {
        if (!extension_loaded('hash')) {
            require_once 'Kizano/Auth/Adapter/Exception.php';
            throw new Kizano_Auth_Adapter_Exception(__CLASS__  . ' requires the \'hash\' extension');
        }

        $front = Zend_Controller_Front::getInstance();
        $this->_request = $front->getRequest();
        $this->_response = $front->getResponse();
        $this->_user = Model_DbTable_User::getInstance();
        $this->_login = new ArrayObject(array(), ArrayObject::ARRAY_AS_PROPS);
    }

    /**
     * Attempts to authenticate the user.
     *  
     * @return boolean
     */
    public function authenticate()
    {
        # First gather the necessary data.
        $params = $this->_request->getParams();
        $result = false; # A user should fail by default, they are authenticating here.

        # If the given credentials are not provided by the end-user
        if (!isset($params['username']) || !isset($params['password'])) {
            $result = new Zend_Auth_Result(
                Zend_Auth_Result::FAILURE,
                null,
                array('Empty login credentials!')
            );
        }

        # If the sought user isn't found in the DB.
        $user = $this->_user
            ->fetchRow(array('username = ?' => $params['username']));
        if (!$user) {
            $result = new Zend_Auth_Result(
                Zend_Auth_Result::FAILURE_IDENTITY_NOT_FOUND,
                $params['username'],
                array('Invalid login credentials.')
            );
        } else {
            $user = $user->toArray();
        }
        # Mock up the hashed password and compare results.
        $hash = Kizano_Strings::hashPass($params['password'], $user['salt']);
        if ($hash === $user['password']) {
            $this->_login['user_id']        = $user['user_id'];
            #$this->_login['group_id']        = $this->getGroupID();
            #$this->_login['type']            = $this->getUserType();
            $this->_login['email']            = $user['email'];
            $this->_login['username']        = $user['username'];
            #$this->_login['name']            = $this->getNames();
            $result = new Zend_Auth_Result(
                Zend_Auth_Result::SUCCESS,
                $this->_login,
                array('Successful Login!')
            );
        }

        # If all the above checks fail, then the user is invalid.
        if (!$result) {
            $result = new Zend_Auth_Result(
                Zend_Auth_Result::FAILURE_IDENTITY_NOT_FOUND,
                $params['username'],
                array('Invalid login credentials.')
            );
        }

        return $result;
    }

    /**
     * Retrieves the username from the attempted login.
     *  
     * @return string
     */
    public function getUsername()
    {
        return $this->_login['username'];
    }

    /**
     * Retrieves the username from the attempted login.
     *  
     * @return string
     */
    public function getEmail()
    {
        return $this->_login['email'];
    }

    /**
     * Retrieves the `user_id` from the DB based on this authenticated user.
     *  
     * @return integer
     */
    public function getUserId()
    {
        return (integer)$this->_login['user_id'];
    }

    /**
     * Gets the user's active status
     *  
     * @return boolean
     */
    public function getActive()
    {
        return $this->_login['active'];
    }

    /**
     * Gets the user's paid status
     *  
     * @return boolean
     */
    public function getPaid()
    {
        return $this->_login['paid'];
    }
}

