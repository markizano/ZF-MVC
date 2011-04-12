<?php
/**
 *  Kizano_Acl
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
 *  @package    Acl
 *  @copyright  Copyright (c) 2009-2011 Markizano Draconus <markizano@markizano.net>
 *  @license    http://framework.zend.com/license/new-bsd     New BSD License
 *  @author     Markizano Draconus <markizano@markizano.net>
 */

/**
 *  Holds the ACL for managing user sessions.
 *
 *  @category   Kizano
 *  @package    Acl
 *  @copyright  Copyright (c) 2009-2011 Markizano Draconus <markizano@markizano.net>
 *  @license    http://framework.zend.com/license/new-bsd     New BSD License
 *  @author     Markizano Draconus <markizano@markizano.net>
 */
class Kizano_Acl extends Zend_Acl
{
    /**
     * Configuration options.
     * 
     * @var array
     */
    protected $_options;

    /**
     * Debugging option
     * 
     * @var boolean
     */
    protected $_debug;

    public function __construct($options = array())
    {
        $this->setOptions($options);
    }

    /**
     * Sets configuration options
     * 
     * @param Zend_Config|array     $options    The options to set.
     * 
     * @return void
     */
    public function setOptions($options)
    {
        if ($options instanceof Zend_Config) {
            $options = $options->toArray();
        }

        if (!is_array($options)) {
            throw new InvalidArgumentException(sprintf(
                '%s(): Expected array for the options, received %s',
                __METHOD__,
                gettype($options)
            ));
        }
        $this->_options = $options;
        if (isset($options['debug'])) {
            $this->_debug = (bool)$options['debug'];
        }
    }

    /**
     * Gets configuration options.
     * 
     * @return array
     */
    public function getOptions()
    {
        return $this->_options;
    }

    /**
     * Define the roles and resources, then setup the permissions for them.
     * 
     * @return void
     */
    public function setupRoles()
    {
        # Add the roles to the ACL list registry.
        foreach ($this->_options['roles'] as $role => $parent) {
            $role = $this->_getRole($role);
            if (isset($parent['parent'])) {
                if ($this->_debug) print "\$this->addRole($role, $parent[parent]);<br />\n";
                $this->addRole($role, $parent['parent']);
            }else{
                if ($this->_debug) print "\$this->addRole($role);<br />\n";
                $this->addRole($role);
            }
        }
        foreach ($this->_options['modules'] as $module => $moduleConfig) {
            if (!is_array($moduleConfig)) {
                continue;
            }
            # Next, add the resources to the ACL list.
            foreach ($moduleConfig as $controller => $controllerConfig) {
                foreach ($controllerConfig as $action => $privileges) {
                    if ($action == 'all') {
                        trigger_error("Privilege action `all' is deprecated. " .
                            "Found in module($module)/controller($controller)/action($action)",
                            E_USER_WARNING);
                    }

                    $resource = $this->_getResource($module, $controller, $action);
                    if (!$this->has($resource)) {
                        if ($this->_debug) print "\$this->addResource($resource);<br />\n";
                        $this->addResource($resource);
                    }
                    // If there were invalid privileges assigned, skip them.
                    if (!is_array($privileges) || !count($privileges)) {
                        continue;
                    }
                    // Next we attempt to setup the privileges
                    foreach ($privileges as $allowDeny => $roles) {
                        # If we have multiple roles to process
                        if (is_array($roles)) {
                            foreach ($roles as $role) {
                                $this->allowDeny($role, $resource, $action, $allowDeny);
                            }
                        } else {
                            $this->allowDeny($roles, $resource, $action, $allowDeny);
                        }
                    }
                }
            }
        }
    }

    /**
     * Sets up the allow/deny rules for a given role.
     * 
     * @param string    $role       The role ID to use.
     * @param string    $resource   The resource ID to use.
     * @param string    $privilege  The privilege ID to use.
     * @param enum      $grant      (allow|deny)
     * 
     * @return void
     */
    public function allowDeny($role, $resource, $privilege, $grant = 'deny')
    {
        if ($this->_debug) print "\$this->$grant($role, $resource, $privilege);<br />\n";
        $this->$grant($role, $resource, $privilege);
    }

    /**
     * Setup the default allow/deny access rights.
     * 
     * @return void
     */
    public function setupDefaults()
    {
        if (isset($this->_options['super']['role'])) {
            $this->allow($this->_options['super']['role']);
        }
        if (isset($this->_options['default']['resource']) && !$this->has($this->_options['default']['resource'])) {
            $this->addResource($this->_options['default']['resource']);
        }
    }

    /**
     * Gets a usable resource name based on the type provided.
     * 
     * @param stirng    $module     The module name.
     * @param string    $controller The controller name.
     * 
     * @return string
     */
    protected function _getResource($module, $controller, $action = null) {
        if ($action) {
            return "{$module}_{$controller}_$action";
        } else {
            return "{$module}_{$controller}";
        }
    }

    /**
     * Checks to see if a given role has access to a resource
     * 
     * @param string    $role        The user role
     * @param string    $resource    The resource the user wants to access.
     * @param string    $privilege   The privilege the user has.
     * 
     * @return boolean
     */
    public function isAllowed($role = null, $resource = null, $privilege = null) {
        $role = $this->hasRole($role) ? $role : $this->_options['default']['role'];
        $resource = $this->has($resource)? $resource : $this->_options['default']['resource'];
        return (bool)parent::isAllowed($role, $resource, $privilege);
    }

    /**
     *  Gets a role based on the registered roles with the model.
     *  
     *  @param string   $role   The role to check.
     *  
     *  @return string
     */
    protected function _getRole($role)
    {
        // We can't depend entirely on the application, but if it's there, use it.
        if (!class_exists('Model_User')) {
            return $this->_options['default']['role'];
        }

        switch ($role) {
            case 'admin':       return Model_User::TYPE_ADMIN;
            default:            return $this->_options['default']['role'];
        }
    }
}

