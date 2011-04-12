<?php
/**
 *  Kizano_IndexControllerTest
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
 *  Serves as an example unit tests against a controller.
 *
 *  @category   Kizano
 *  @package    Controller
 *  @copyright  Copyright (c) 2009-2011 Markizano Draconus <markizano@markizano.net>
 *  @license    http://framework.zend.com/license/new-bsd     New BSD License
 *  @author     Markizano Draconus <markizano@markizano.net>
 */
class IndexControllerTest extends Kizano_Test_PHPUnit_ControllerTestCase
{
    /**
     *  Ensures the home page loads.
     *  
     *  @return void
     */
    public function testIndexAction()
    {
        $this->dispatch('/');
        $body = $this->response->getBody();
        $this->assertContains('<html', $body, 'Failed to verify the home page loads.');
    }
}

