<?php
/**
 * Clickbooth
 *
 * @category   Ic
 * @package    Kp_View
 * @subpackage Helper
 * 
 * Similar to Zend_View_Helper_FormNote, only we wrap it in a span and add the attributes.
 */
class Kp_View_Helper_FormNote extends Zend_View_Helper_FormElement
{
    public function formNote($name, $value = null, $attribs = null)
    {
        $info = $this->_getInfo($name, $value, $attribs);
        extract($info); // name, value, attribs, options, listsep, disable

        $xhtml = '<span'
               . ' id="' . $this->view->escape($id) . '"'
               . $this->_htmlAttribs($attribs)
               . '>'
               . $value
               . '</span>';

        return $xhtml;
    }
}