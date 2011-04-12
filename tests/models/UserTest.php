<?php
/**
 *  Kizano_Model_User
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
 *  @package    Model
 *  @copyright  Copyright (c) 2009-2011 Markizano Draconus <markizano@markizano.net>
 *  @license    http://framework.zend.com/license/new-bsd     New BSD License
 *  @author     Markizano Draconus <markizano@markizano.net>
 */

/**
 *  Ensures the model is manipulating the user instance properly.
 *
 *  @category   Kizano
 *  @package    Model
 *  @copyright  Copyright (c) 2009-2011 Markizano Draconus <markizano@markizano.net>
 *  @license    http://framework.zend.com/license/new-bsd     New BSD License
 *  @author     Markizano Draconus <markizano@markizano.net>
 */
class Model_UserTest extends Kizano_Test_PHPUnit_ControllerTestCase
{
    /**
     *  Holds an instance of the model we are testing.
     *  
     *  @var Model_User
     */
    protected $_model;

    /**
     *  Bootstraps this test case.
     *  
     *  @return void
     */
    public function setup()
    {
        parent::setup();
        $this->_model = new Model_User;
    }

    /**
     *  Garbage collection...
     *  
     *  @return void
     */
    public function tearDown()
    {
        unset($this->_model);
    }

    /**
     *  Ensures the user object is returning an instance of the user as expected.
     *  Asserts valid input into the method with results.
     *  
     *  @return void
     */
    public function testGetUser_PassWithResult()
    {
        $expected = array(
            array(
                'username' => 'test-user',
                'password' => 'F8377C90FCFD699F0DDBDCB30C2C9183D2D933EA',
                'email' => 'test@example.com',
            )
        );

        $mockArray = $this->getMock('Zend_Db_Table_Row');
        $mockArray->expects($this->once())
            ->method('toArray')
            ->will($this->returnValue($expected));

        $mockTable = $this->getMock('Model_DbTable_User', array(), array(), '', false);
        $mockTable->expects($this->once())
            ->method('find')
            ->will($this->returnValue($mockArray));

        // Inject the mock object into the model so it calls that instead of the real thing.
        $this->_model->setTable($mockTable);

        // Perform the query.
        $actual = $this->_model->getUser(1);

        // Assert results.
        $this->assertEquals(array_shift($expected), $actual, 'Failed to verify the method returns a user.');
    }

    /**
     *  Ensures the user object is returning an instance of the user as expected.
     *  Asserts valid input into the method without results.
     *  
     *  @return void
     */
    public function testGetUser_PassEmpty()
    {
        $expected = array();

        // First generate a mock object of the Zend_Db result set.
        $mockArray = $this->getMock('Zend_Db_Table_Row');
        $mockArray->expects($this->once())
            ->method('toArray')
            ->will($this->returnValue($expected));

        // Next create a mock object of the table class.
        $mockTable = $this->getMock('Model_DbTable_User', array(), array(), '', false);
        $mockTable->expects($this->once())
            ->method('find')
            ->will($this->returnValue($mockArray));

        // Inject the mock object into the model so it calls that instead of the real thing.
        $this->_model->setTable($mockTable);

        // Perform the query.
        $actual = $this->_model->getUser(1);

        // Assert results.
        $this->assertEquals($expected, $actual, 'Failed to verify the method returns a user.');
    }

    /**
     *  Ensures the user object is returning an instance of the user as expected.
     *  Asserts invalid input into the method.
     *  
     *  @return void
     */
    public function testGetUser_Fail()
    {
        // Assert null.
        $this->setExpectedException('InvalidArgumentException');
        $this->_model->getUser(null);

        // Assert empty integer.
        $this->setExpectedException('InvalidArgumentException');
        $this->_model->getUser(0);

        // Assert array.
        $this->setExpectedException('InvalidArgumentException');
        $this->_model->getUser(array());

        // Assert object.
        $this->setExpectedException('InvalidArgumentException');
        $this->_model->getUser(new stdClass);
    }
}

