<?php
/**
 *  Kizano_View_Helper_HeadLink
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
 *  @package    View
 *  @copyright  Copyright (c) 2009-2011 Markizano Draconus <markizano@markizano.net>
 *  @license    http://framework.zend.com/license/new-bsd     New BSD License
 *  @author     Markizano Draconus <markizano@markizano.net>
 */

/**
 * Overrides the native ZF view helper to prepend the base URL for
 * stylesheets to provide stylesheet paths.
 *
 *  @category   Kizano
 *  @package    View
 *  @copyright  Copyright (c) 2009-2011 Markizano Draconus <markizano@markizano.net>
 *  @license    http://framework.zend.com/license/new-bsd     New BSD License
 *  @author     Markizano Draconus <markizano@markizano.net>
 */
class Kizano_View_Helper_HeadLink extends Zend_View_Helper_HeadLink
{
	/**
	 * Prefixes a given stylesheet path with a base URL for stylesheets
	 * before rendering the markup for a link element to include it.
	 *
	 * @param stdClass $item Object representing the stylesheet to render
	 * @return string Markup for the rendered link element
	 */
	public function itemToString(stdClass $item)
	{
		if($this->_isAdminPage(Zend_Controller_Front::getInstance()->getRequest())) {
			$item->href = WEB_CSS.ltrim($item->href, '/');
		}

		return parent::itemToString($item);
	}

	private function _isAdminPage($request)
	{
		if ($request->getParam('module') != 'public'
		    && $request->getParam('controller') != 'public'
		    && !$request->isXmlHttpRequest())
	    {
			return true;
		}

		return false;
	}
}

