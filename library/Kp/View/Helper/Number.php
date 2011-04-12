<?php
/**
 * Clickbooth
 *
 * @category   Ic
 * @package    Kp_View
 * @subpackage Helper
 */

/** Zend_View_Helper_Abstract.php */
require_once 'Zend/View/Helper/Abstract.php';

/**
 * Simple Number formatting.
 */
class Kp_View_Helper_Number extends Zend_View_Helper_FormElement
{
    /**
     * Formats value if numeric, otherwise returns default.
     */
    public function number($value, $decimals = 0, $thousands_sep = ',', $default = 'N/A')
    {
        if (!is_numeric($value)) {
            return $default;
        }

        if($decimals == 0) {
            return number_format($value);
        } else {
            return number_format($value, $decimals, '.', $thousands_sep);
        }
    }
}