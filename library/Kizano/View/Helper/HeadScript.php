<?php
/**
 *  Kizano_View_Helper_HeadScript
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
 * scripts to provide script paths.
 *
 *  @category   Kizano
 *  @package    View
 *  @copyright  Copyright (c) 2009-2011 Markizano Draconus <markizano@markizano.net>
 *  @license    http://framework.zend.com/license/new-bsd     New BSD License
 *  @author     Markizano Draconus <markizano@markizano.net>
 */
class Kizano_View_Helper_HeadScript extends Zend_View_Helper_HeadScript
{
	/**
	 * Prefixes a given script path with a base URL for scripts before  
	 * rendering the markup for a script element to include it.
	 *
	 * @param stdClass $item Object representing the script to render
	 * @param string $indent
	 * @param string $escapeStart
	 * @param string $escapeEnd
	 *
	 * @return string Markup for the rendered script element
	 */
	public function itemToString($item, $indent, $escapeStart, $escapeEnd)
	{
		if (isset($item->attributes['src'])) {
			$item->attributes['src'] = WEB_JS.ltrim($item->attributes['src'], '/');
		}

		$result = parent::itemToString($item, $indent, $escapeStart, $escapeEnd);

		return $result;
	}
}

