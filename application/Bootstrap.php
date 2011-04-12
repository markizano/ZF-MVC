<?php
/**
 *  Bootstrap
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
 *  @category   ZF
 *  @package    Bootstrap
 *  @copyright  Copyright (c) 2009-2011 Markizano Draconus <markizano@markizano.net>
 *  @license    http://framework.zend.com/license/new-bsd     New BSD License
 *  @author     Markizano Draconus <markizano@markizano.net>
 */

/**
 *  Bootstraps the application.
 *
 *  @category   ZF
 *  @package    Bootstrap
 *  @copyright  Copyright (c) 2009-2011 Markizano Draconus <markizano@markizano.net>
 *  @license    http://framework.zend.com/license/new-bsd     New BSD License
 *  @author     Markizano Draconus <markizano@markizano.net>
 */
class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{
    /**
     * Execute this application!
     * 
     * @return void
     */
    public function run()
    {
        require 'Kizano.php';
        $this->_environment = ENVIRONMENT;
        $this->bootstrap();
        $this->frontController->dispatch();
    }

    /**
     * Starts up the autoloader.
     * 
     * @return void
     */
    public function _initAutoload()
    {
        $autoloader = new Zend_Application_Module_Autoloader(array(
            'namespace' => '',
            'basePath'  => dirname(__FILE__),
            'resourceTypes' => $this->_options['moduleResources']
        ));

        $loader = $this->getResourceLoader();
        $loader->addResourceTypes($this->_options['moduleResources']);
    }

    /**
     * Initializes the configuration
     * 
     * @return        Array        The configuration loaded.
     */
    protected function _initConfigs()
    {
        Zend_Registry::isRegistered('config')
            || Zend_Registry::set('config', $this->_options, true);
        return $this->_options;
    }

    /**
     * For some reason, ZF won't register the cachemanager in the registry.
     * We do it manually here.
     * 
     * @return void
     */
    protected function _initCache()
    {
        $this->bootstrap('cachemanager');
        Zend_Registry::set('cachemanager', $this->getResource('cachemanager'));
    }

    /**
     *  Stores an instance of the logger in the registry.
     *  
     *  @return void
     */
    protected function _initLogger()
    {
        $this->bootstrap('log');
        Zend_Registry::set('log', $this->getResource('log'));
    }

    /**
     *  Sets the database resource to the registry.
     *  
     *  @return void
     */
    protected function _initDatabase()
    {
        $this->bootstrap('db');
        $db = $this->getResource('db');
        Zend_Registry::set('db', $db);
        return $db;
    }

    /**
     * Initializes the current session and assigns it to the registry
     * 
     * @return Zend_Session
     */
    protected function _initSession()
    {
        $sess = $this->_options['session'];
        $session = new Zend_Session_Namespace($sess['name']);
        Zend_Registry::set('session', $session);
        return $session;
    }

    /**
     * Initializes the authentication component.
     * 
     * @return Zend_Auth
     */
    protected function _initAuth()
    {
        $auth = Zend_Auth::getInstance();
        $auth->setStorage(new Kizano_Auth_Storage_Session());
        return $auth;
    }

    /**
     * Initializes the View controller
     * 
     * @return Zend_View
     */
    protected function _initViews()
    {
        $this->bootstrap('view');
        $layout = $this->getResource('layout');
        $this->view = $this->getResource('view');

        $this->view->headMeta()->setIndent(4);
        $this->view->headLink()->setIndent(4);
        $this->view->headScript()->setIndent(4);

        $render = Zend_Controller_Action_HelperBroker::getStaticHelper('ViewRenderer');
        $render->setView($this->view)
            ->setViewScriptPathSpec(':controller/:action.:suffix')
            ->setViewScriptPathNoControllerSpec(':action.:suffix');
        $layout->setView($this->view);

        Zend_Registry::set('layout', $layout);
        Zend_Registry::set('view', $this->view);

        return $this->view;
    }

    /**
     * Initializes the front controller.
     * 
     * @return Zend_Controller_Front
     */
    protected function _initFront()
    {
        $this->bootstrap('frontcontroller');
        $this->frontController->unRegisterPlugin('Zend_Layout_Controller_Plugin_Layout');
        return $this->frontController;
    }

    /**
     * Initializes the modules and registers their namespaces to ensure easy loading.
     * 
     * @return void
     */
    protected function _initModule()
    {
        $modules = $this->_options['resources']['modules'];
        foreach (array_keys($modules) as $module) {
            $controllers[$module] = DIR_APPLICATION . 'modules' . DS . $module . DS . 'controllers';
        }

        $this->frontController->setControllerDirectory($controllers);
        $this->frontController->addControllerDirectory(DIR_APPLICATION . 'controllers');
    }

    /**
     * Inits routes associated with the default controllers.
     * 
     * @return Zend_Controller_Router_Route
     * /
    protected function _initRoutes()
    {
        $front = $this->getResource('frontController');
        $router = $front->getRouter();
        $router->addRoute(
            'image',
            new Zend_Controller_Router_Route_Regex(
                'library/images/user/(\w+)\.(\d+x\d+|\w+)\.(\w+)',
                array(
                    'module'        => 'default',
                    'controller'    => 'index',
                    'action'        => 'image',
                ),
                array(
                    1                => 'hash',
                    2                => 'scale',
                    3                => 'ext',
                )
            )
        );
    } //*/
    
}

