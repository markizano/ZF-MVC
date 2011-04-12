<?php
/**
 *  Kizano_Test_PHPUnit_ControllerTestCase
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
 *  @package    Test
 *  @copyright  Copyright (c) 2009-2011 Markizano Draconus <markizano@markizano.net>
 *  @license    http://framework.zend.com/license/new-bsd     New BSD License
 *  @author     Markizano Draconus <markizano@markizano.net>
 */

/**
 *  Component to help us unit test controllers and models more efficiently.
 *
 *  @category   Kizano
 *  @package    Test
 *  @copyright  Copyright (c) 2009-2011 Markizano Draconus <markizano@markizano.net>
 *  @license    http://framework.zend.com/license/new-bsd     New BSD License
 *  @author     Markizano Draconus <markizano@markizano.net>
 */
class Kizano_Test_PHPUnit_ControllerTestCase extends Zend_Test_PHPUnit_ControllerTestCase
{
    /**
     *  Holds application config options.
     *  
     *  @var Array
     */
    protected $_options;

    /**
     * Application configuration
     *
     * @var Zend_Config_Ini
     */
    private $__configuration = null;

    /**
     * Allow to override the global configuration for a particular test case
     *
     * @var string
     */
    protected $_seedFilesPath = null;

    /**
     * Allow to override the global configuration for a particular test case
     *
     * @var string
     */
    protected $_expectedFilesPath = null;

    /**
     * Executed before each of the unit tests.
     *
     * @return void
     */
    public function setUp()
    {
        $this->bootstrap = array($this, 'appBootstrap');
        parent::setUp();
    }

    /**
     * Bootstraps the Cb application.
     *
     * @return void
     */
    public function appBootstrap()
    {
        $app = Zend_Registry::get('application');
        $app->bootstrap();
        $this->_options = $app->getOptions();
    }

    /**
     * Called after each unit test. Cleans up the request and reponse objects.
     * 
     * @note This function is not implemented in Zend_Test_PHPUnit_ControllerTestCase, and it has no
     *  function contents in PHPUnit_Framework_TestCase. Therefore, we must implement the cleanup here.
     * 
     * @return void
     */
    public function tearDown()
    {
        // Not implemented in parent classes.
        // parent::tearDown();
        $this->reset();
    }

    /**
     *  Authenticates the user according to the application sepcifics.
     *  
     *  @return void
     */
    public function authenticate()
    {
        $session = Zend_Registry::get('session');
        $session->authentication = new ArrayObject(
            Model_User::getAuthUser(),
            ArrayObject::ARRAY_AS_PROPS
        );
    }

    /**
     * Returns the seed files folder path
     *
     * @return string
     */
    public function getSeedFilesPath()
    {
        if ($this->_seedFilesPath == null) {
            $seedFilesPath = $this->getConfiguration()->tests->seeds->folder;
            $this->_seedFilesPath = rtrim($seedFilesPath, '/') . '/';
        }

        return $this->_seedFilesPath;
    }

    /**
     * Returns the expected files folder path
     *
     * @return string
     */
    public function getExpectedFilesPath()
    {
        if ($this->_expectedFilesPath == null) {
            $filesPath = $this->getConfiguration()->tests->expected->folder;
            $this->_expectedFilesPath = rtrim($filesPath, '/') . '/';
        }

        return $this->_expectedFilesPath;
    }
}

