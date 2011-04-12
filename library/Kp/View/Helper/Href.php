<?php
/**
 * @category   Ic
 * @package    Kp_View
 * @subpackage Helper
 * @version    $Id$
 *
 * Helper for making easy anchor tags and getting urls that depend on the routes and router
 *
 * @package    Kp_View
 * @subpackage Helper
 * @author     James Solomon <james@integraclick.com>
 */
class Kp_View_Helper_Href extends Zend_View_Helper_Abstract
{
    /**
     * Generates an <a> tag witht he supplied information.
     *
     * @access public
     * 
     * @param  string $urlText The 
     * @param  array  $urlOptions Options passed to the assemble method of the Route object.
     * @param  array  $tagOptions Valid tag attributes to be built
     * @return string Url for the link href attribute.
     */
    public function href($urlText, array $urlOptions = array(), $tagOptions = array())
    {
        $url = new Zend_View_Helper_Url();
        $uri = $url->url($urlOptions);
        
        $attrs = '';
        if (!empty($tagOptions)) {
            $attrParts = array();
            foreach ($tagOptions as $k => $v) {
                $attrParts[] = $k . '="' . $v . '"';
            }
            $attrs = implode(' ', $attrParts);
        }
        
        $href = '<a ' . $attrs . ' href="' . $uri . '">' . $urlText . '</a>';
        
        return $href;
    }
}
