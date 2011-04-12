<?php
/**
 *  Kizano_Form
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
 *  Form extension for improving Zend_Form's functionality.
 *
 *  @category   Kizano
 *  @package    Form
 *  @copyright  Copyright (c) 2009-2011 Markizano Draconus <markizano@markizano.net>
 *  @license    http://framework.zend.com/license/new-bsd     New BSD License
 *  @author     Markizano Draconus <markizano@markizano.net>
 */
class Kizano_Form extends Zend_Form
{

	/**
	 *  Holds the name of the current form
	 *  
	 *  @var string
	 */
	protected $_formName;

	/**
	 *  Holds the fields to render in the form
	 *  
	 *  @var Array
	 */
	protected $_fields = array();

	/**
	 *  Default configuration to inject into the form
	 *  
	 *  @var Array
	 */
	protected $_defaults = array();

	/**
	 *  View instance to help with the rendering
	 *  
	 *  @var Zend_View 
	 */
	public $view;

	/**
	 *	Generates a new instance of this formset
	 *  
	 *	@param	Zend_Config		options		(Optional) Zend_Form Configuration options
	 *  
	 *	@return void
	 */
	public function __construct($options = null)
	{

        if (is_array($options)) {
            $this->setOptions($options);
        } elseif ($options instanceof Zend_Config) {
            $this->setConfig($options);
        }

		$this->view = Zend_Registry::get('view');
		$this->setName('Default_Form');
		$this->setAction('');
		$this->setDisableLoadDefaultDecorators(true);
		$this->clearDecorators();

        // Extensions...
        $this->init();
	}

	/**
	 *	Gets a particular form of this extension.
	 *  
	 *	@param	name		string		The name of the form extension to obtain.
	 *  
	 *	@return	Kizano_Form
	 *	@throws	Kizano_Form_Exception
	 */
	public static function getForm($name) {
		if (!is_string($name)) {
			throw new InvalidArgumentException(sprintf(
				"%s::%s(): @param \$name: Expected(string); Received(%s).",
				__CLASS__,
				__FUNCTION__,
				getType($name)
			));
			return false;
		}

		$formName = 'Kizano_Form_'.ucWords($name);

		if (!class_exists($formName, false)) {
			Zend_Loader::loadClass($formName);
		}

		$form = new $formName;
		$form->$name();
		if (!$form) return false;

		return $form;
	}

	/**
	 *	Adds a form element to this form.
	 *  
	 *	@param name		string				The name of the field to add.
	 *	@param type		string				The fully qualified classname of the field to add.
	 *	@param options	array|Zend_Config	Configuration options to add to the element.
	 *  
	 *	@return Kizano_Form
	 */
	public function addField($name, $type, $options = array()) {
		if (!is_string($name)) {
			throw new Kizano_Form_Exception(sprintf(
				"%s::%s(): Param type of \$name; Expected (string), Received (%s)",
				__CLASS__,
				__FUNCTION__,
				get_type($name)
			));
			return false;
		}

		if (!is_string($type)) {
			throw new Kizano_Form_Exception(sprintf(
				"%s::%s(): Param type of \$type; Expected (string), Received (%s)",
				__CLASS__,
				__FUNCTION__,
				get_type($type)
			));
			return false;
		}

		if (!is_array($options) && !($options instanceof Zend_Config)) {
			throw new Kizano_Form_Exception(sprintf(
				"%s::%s(): Param type of \$options; Expected (array|Zend_Config), Received (%s)",
				__CLASS__,
				__FUNCTION__,
				get_type($options)
			));
			return false;
		}

		$this->_fields[$name] = new $type($name, $options);
		return $this;
	}

	/**
	 *	Adds validators, filters, appends a submit button, generates and renders 
	 *		this instance of a form.
	 *  
	 *	@return Kizano_Form
	 */
	public function finalizeForm()
	{
		# Add internal validators
		$this->_validate();
		# Add internal filters
		$this->_filter();

		# Add a submit button
		$this->addField(
			'submit',
			'Zend_Form_Element_Submit',
			array(
				'label'		=> 'Submit',
				'value'		=> 'Submit',
				'onclick'	=> 'this.disable = true',
			)
		);
		$this->setElements($this->getFields());
		$this->setElementDecorators(array('ViewHelper', new Kizano_Form_Decorator(array('tag'=>'div'))));
		return $this;
	}

	/**
	 *	Placeholder for adding filters.
	 *  
	 *	@return void
	 */
	protected function _filter()
	{}

	/**
	 *	Placeholder for adding validators.
	 *  
	 *	@return void
	 */
	protected function _validate()
	{
		foreach ($this->_fields as $name => $field) {
			if ($field->isRequired()) {
				$this->_fields[$name]->addValidator(new Zend_Validate_NotEmpty);
			}
		}
	}

	/**
	 *	Magic function to return the string representation of this form.
	 *  
	 *	@return string
	 */
	public function __toString()
	{
		return $this->render($this->view);
	}
}

