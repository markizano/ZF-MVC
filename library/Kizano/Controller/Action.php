<?php
/**
 *  Kizano_Controller_Action
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
 *  Action controller extension to provide us with some additional functionality
 *  in the module controllers.
 *
 *  @category   Kizano
 *  @package    Controller
 *  @copyright  Copyright (c) 2009-2011 Markizano Draconus <markizano@markizano.net>
 *  @license    http://framework.zend.com/license/new-bsd     New BSD License
 *  @author     Markizano Draconus <markizano@markizano.net>
 */
class Kizano_Controller_Action extends Zend_Controller_Action
{
    /**
     *  Quick-access for the current session in place.
     *  
     *  @var Zend_Session_Namespace
     */
    protected $_session;

    /**
     *  Quick-access to the cache manager.
     *  
     *  @var Zend_Cache_Core
     */
    protected $_cache;

    /**
     *  Constructs this instance of a controller.
     *  
     *  @return void
     */
    public function init()
    {
        $this->_session = Zend_Registry::get('session');
        $this->_cache   = Zend_Registry::get('cachemanager');
    }

    /**
     *  Quick-handy function to disable the layout/view.
     *
     *  @param Boolean  $view   Whether to disable the view as well.
     *
     *  @return void
     */
    protected function _disableLayout($view = false)
    {
        $this->_helper->layout()->disableLayout();
        if (!$view) {
            $this->_helper->viewRenderer->setNoRender(true);
        }
    }
}

