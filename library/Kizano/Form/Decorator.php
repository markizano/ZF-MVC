<?php
/**
 *  Kizano_Form_Decorator
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
 *  @package    Form
 *  @copyright  Copyright (c) 2009-2011 Markizano Draconus <markizano@markizano.net>
 *  @license    http://framework.zend.com/license/new-bsd     New BSD License
 *  @author     Markizano Draconus <markizano@markizano.net>
 */

/**
 *  Form element decorator.
 *
 *  @category   Kizano
 *  @package    Form
 *  @copyright  Copyright (c) 2009-2011 Markizano Draconus <markizano@markizano.net>
 *  @license    http://framework.zend.com/license/new-bsd     New BSD License
 *  @author     Markizano Draconus <markizano@markizano.net>
 */
class Kizano_Form_Decorator
	extends Zend_Form_Decorator_Abstract
		implements Zend_Form_Decorator_Marker_File_Interface
{
    /**
     *  Builds up the label for this instance of an element.
     *  
     *  @return string
     */
	public function buildLabel()
	{
		$element = $this->getElement();
		if (strIpos($element->id, 'submit') !== false) return null;
		$label = $element->getLabel();

		if ($translator = $element->getTranslator()) {
			$label = $translator->translate($label);
		}

		if ($element->isRequired()) {
			$label .= '<span class="req">*</span>';
		}

		return $label;
	}

    /**
     *  Builds up the full input element.
     *  
     *  @return string
     */
	public function buildInput()
	{
		$element = $this->getElement();
		$helper	= $element->helper;
		return $element->getView()->$helper(
			$element->getName(),
			$element->getValue(),
			$element->getAttribs(),
			$element->options
		);
	}

    /**
     *  Builds up the error labels for this element.
     *  
     *  @return string
     */
	public function buildErrors()
	{
		$element	= $this->getElement();
		$messages = $element->getMessages();

		if (empty($messages)) {
		    return '';
	    }

		return "<div class='errors'>{$element->getView()->formErrors($messages)}</div>";
	}

    /**
     *  Builds up the description for this element.
     *  
     *  @return string
     */
	public function buildDescription()
	{
		$element = $this->getElement();
		$desc	= $element->getDescription();

		if (empty($desc)) {
			return '';
		}

		return "<div class='description'>$desc</div>";
	}

    /**
     *  Finally, we render the element.
     *  
     *  @param string  $content     The main content of the element.
     *  
     *  @return string
     */
	public function render($content)
	{
	    if (!is_string($content)) {
	        throw new InvalidArgumentException('Argument 1 ($content) expected string.');
	    }

		$element = $this->getElement();
		if (!$element instanceof Zend_Form_Element) {
			return $content;
		}

		if ($element->getView() === null) {
			return $content;
		}

		$content	= trim($content);
		$label		= $this->buildLabel();
		$input		= $this->buildInput();
		$errors		= $this->buildErrors();
		$desc		= $this->buildDescription();

		if ($element instanceof Zend_Form_Element_Multi) {
			$select = ' _select';
		} elseif ($element instanceof Zend_Form_Element_Submit) {
			$select = ' _submit';
		} else {
			$select = null;
		}

		return "\t\t\t\t\t\t<div class='element$select'>$errors$label$content$desc</div>\n";
	}
}

