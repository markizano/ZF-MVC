<?php
/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category   Zend
 * @package    Zend_Application
 * @subpackage Resource
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id: View.php 22965 2010-09-18 17:45:51Z intiilapa $
 */

/**
 * Resource for settings view options
 *
 * @uses       Zend_Application_Resource_ResourceAbstract
 * @category   Zend
 * @package    Zend_Application
 * @subpackage Resource
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Kizano_Application_Resource_View extends Zend_Application_Resource_ResourceAbstract
{
    /**
     * @var Zend_View_Interface
     */
    protected $_view;

    /**
     * Defined by Zend_Application_Resource_Resource
     *
     * @return Zend_View
     */
    public function init()
    {
        $view = $this->getView();
        return $view;
    }

    /**
     * Retrieve view object
     *
     * @return Zend_View
     */
    public function getView()
    {
        if (null === $this->_view) {
            $this->_bootstrap->bootstrap('layout');
            $options = $this->getOptions();
            $this->_view = $this->_bootstrap->getResource('layout')->getView();

            if (isset($options['doctype'])) {
                $this->_view->doctype()->setDoctype(strtoupper($options['doctype']));
                $this->_view->doctype($options['doctype']);
                if (isset($options['charset']) && $this->_view->doctype()->isHtml5()) {
                    $this->_view->headMeta()->setCharset($options['charset']);
                }
            }

            if (isset($options['contentType'])) {
                $this->_view->headMeta()->appendHttpEquiv('Content-Type', $options['contentType']);
            }

            if (isset($options['helperPaths'])
                && (
                    is_array($options['helperPaths'])
                    || $options['helperPaths'] instanceof Zend_Config
                )
            ) {
                $options['helperPaths'] instanceof Zend_Config && $options = $options->toArray();
                foreach ($options['helperPaths'] as $path => $prefix) {
                    $this->_view->addHelperPath($prefix, $path);
                }
            }

            if (isset($options['scriptPaths'])
                && (
                    is_array($options['scriptPaths'])
                    || $options['scriptPaths'] instanceof Zend_Config
                )
            ) {
                foreach ($options['scriptPaths'] as $prefix => $path) {
                    $this->_view->addScriptPath($path, $prefix);
                }
            }
        }

        return $this->_view;
    }
}

