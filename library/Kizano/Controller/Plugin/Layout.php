<?php
/**
 *  Kizano_Layout_Plugins_Layout
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
 *  @package    Layout
 *  @copyright  Copyright (c) 2009-2011 Markizano Draconus <markizano@markizano.net>
 *  @license    http://framework.zend.com/license/new-bsd     New BSD License
 *  @author     Markizano Draconus <markizano@markizano.net>
 */

/**
 *  Plugin to override the Zend_Layout_Controller_Plugin_Layout for custom control
 *  over how a page is rendered. Mutliple layouts are also rendered here.
 *
 *  @category   Kizano
 *  @package    Layout
 *  @copyright  Copyright (c) 2009-2011 Markizano Draconus <markizano@markizano.net>
 *  @license    http://framework.zend.com/license/new-bsd     New BSD License
 *  @author     Markizano Draconus <markizano@markizano.net>
 */
class Kizano_Controller_Plugin_Layout extends Zend_Controller_Plugin_Abstract
{
	/**
	 *	Keeps the postDispatch function from iterating and printing more than one layout.
	 */
	protected $_postDispatch = false;

	protected $_layoutActionHelper = null;

	/**
	 * @var Zend_Layout
	 */
	protected $_layout;

	/**
	 * Constructor
	 *
	 * @param  Zend_Layout $layout
	 * @return void
	 */
	public function __construct(Zend_Layout $layout = null)
	{
		if (!is_null($layout)) {
			$this->setLayout($layout);
		}
	}

	/**
	 * Retrieve layout object
	 *
	 * @return Zend_Layout
	 */
	public function getLayout()
	{
		return $this->_layout;
	}

	/**
	 * Set layout object
	 *
	 * @param  Zend_Layout $layout
	 * @return Zend_Layout_Controller_Plugin_Layout
	 */
	public function setLayout(Zend_Layout $layout)
	{
		$this->_layout = $layout;
		return $this;
	}

	/**
	 * Set layout action helper
	 *
	 * @param  Zend_Layout_Controller_Action_Helper_Layout $layoutActionHelper
	 * @return Zend_Layout_Controller_Plugin_Layout
	 */
	public function setLayoutActionHelper(Zend_Layout_Controller_Action_Helper_Layout $layoutActionHelper)
	{
		$this->_layoutActionHelper = $layoutActionHelper;
		return $this;
	}

	/**
	 * Retrieve layout action helper
	 *
	 * @return Zend_Layout_Controller_Action_Helper_Layout
	 */
	public function getLayoutActionHelper()
	{
		return $this->_layoutActionHelper;
	}

	/**
	 *	Pre Dispatch hook - determine the user and which layout to use
	 * 
	 *	@param Zend_Controller_Request_Abstract $request An instance of the Zend Request object
	 * 
	 *	@return void
	 */
	public function preDispatch(Zend_Controller_Request_Abstract $request)
	{
		$layout = Zend_Registry::get('layout');

		if ($request->getModuleName() == 'admin') {
			$layout->setLayoutPath(DIR_APPLICATION . 'layouts/scripts/admin');
		} else {
			$layout->setLayoutPath(DIR_APPLICATION . 'layouts/scripts/site');
		}
	}

	/**
	 * ZF-Hook implemented to render the layout.
	 *
	 * @param  Zend_Controller_Request_Abstract $request
	 *
	 * @return void
	 */
	public function postDispatch(Zend_Controller_Request_Abstract $request) {
		# If we've already post-dispatched, then we shouldn't execute this method
		if ($this->_postDispatch) {
			return null;
		} else {
			$this->_postDispatch = true;
		}

		$layout = Zend_Registry::get('layout');
		$helper = $this->getLayoutActionHelper();

		// Return early if forward detected
		if (!$request->isDispatched()
			|| $this->getResponse()->isRedirect()
			|| (
				$layout->getMvcSuccessfulActionOnly()
				&& (!empty($helper) && !$helper->isActionControllerSuccessful())
			)
		) {
			return;
		}
		// Return early if layout has been disabled
		if (!$layout->isEnabled()) {
			return;
		}

		$response   = $this->getResponse();
		$content	= $response->getBody(true);
		$contentKey = $layout->getContentKey();

		if (isset($content['default'])) {
			$content[$contentKey] = $content['default'];
		}

		if ('default' != $contentKey) {
			unset($content['default']);
		}

		$layout->assign($content);
		$fullContent = null;
		$obStartLevel = ob_get_level();

		try {
			$fullContent = $layout->render();
			$response->setBody($fullContent);
		} catch (Exception $e) {
			while (ob_get_level() > $obStartLevel) {
				$fullContent .= ob_get_clean();
			}

			$request->setParam('layoutFullContent', $fullContent);
			$request->setParam('layoutContent', $layout->content);
			$response->setBody(null);

			throw $e;
		}
	}
}

