<?php
/**
 *  Kizano_Controller_Plugin_Layout
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
 *  ZF-Plugin to setup the navigation.
 *
 *  @category   Kizano
 *  @package    Controller
 *  @copyright  Copyright (c) 2009-2011 Markizano Draconus <markizano@markizano.net>
 *  @license    http://framework.zend.com/license/new-bsd     New BSD License
 *  @author     Markizano Draconus <markizano@markizano.net>
 */
class Kizano_Controller_Plugin_Navigation extends Zend_Controller_Plugin_Abstract
{
    /**
     *  Pre-dispatch marker to prevent unnecessary iteration
     *  
     *  @var Boolean
     */
	protected static $_preDispatch;

	/**
	 * Called before an action is dispatched by Zend_Controller_Dispatcher.
	 *
	 * This callback allows for proxy or filter behavior. By altering the
	 * request and resetting its dispatched flag (via
	 * {@link Zend_Controller_Request_Abstract::setDispatched() setDispatched(false)}),
	 * the current action may be skipped.
	 *
	 * @param  Zend_Controller_Request_Abstract $request
	 * @return void
	 */
	public function preDispatch(Zend_Controller_Request_Abstract $request)
	{
		# For some reason, Zend_Filter likes to iterate over this function. Repetitively.
		if (empty(self::$_preDispatch)) {
		    self::$_preDispatch = true;
	    } else {
	        return false;
        }

		$view = Zend_Registry::get('view');
		$nav = new Zend_Config_Xml(DIR_APPLICATION . 'configs' . DS . 'navigation.xml', 'nav');
		$navigation = new Zend_Navigation($nav);
		$view->navigation()->menu()->setContainer($navigation);
	}
}

