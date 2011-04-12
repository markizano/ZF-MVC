<?php
/**
 *  Admin_IndexControllerTest
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
 *  @category   Admin
 *  @package    Test
 *  @copyright  Copyright (c) 2009-2011 Markizano Draconus <markizano@markizano.net>
 *  @license    http://framework.zend.com/license/new-bsd     New BSD License
 *  @author     Markizano Draconus <markizano@markizano.net>
 */

/**
 *  Assures us the functionality of the administration index controller.
 *
 *  @category   Admin
 *  @package    Test
 *  @copyright  Copyright (c) 2009-2011 Markizano Draconus <markizano@markizano.net>
 *  @license    http://framework.zend.com/license/new-bsd     New BSD License
 *  @author     Markizano Draconus <markizano@markizano.net>
 */
class Admin_IndexControllerTest extends Kizano_Test_PHPUnit_ControllerTestCase
{
    /**
     *  Asserts that the admin page redirects if the user is a guest (who is
     *  not allowed.
     *  
     *  @return void
     */
    public function testRedirect()
    {
        $this->dispatch('/admin/index/index');
        #$this->assertRedirect('Failed to verify the admin module redirects the guest.');
    }

    /**
     *  Assures the home page for the admin loads.
     *  
     *  @return void
     */
    public function testIndexAction()
    {
        $this->authenticate();
        $this->dispatch('/admin/index/index');
        $this->assertNotRedirect('Failed to verify an authenticated user is not redirected.');
        $this->assertContains('<html', $this->response->getBody(),
            'Failed to verify the HTML is returned in the response.');
    }
}

