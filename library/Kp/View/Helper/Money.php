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
 * Simple currency formatting.  Called Money because ZF is already using Currency.
 */
class Kp_View_Helper_Money extends Zend_View_Helper_FormElement
{
    /**
     * Formats value if numeric, otherwise returns default.
     */
    public function money($value, $decimals = 2, $thousands_sep = ',', $default = 'N/A')
    {
        if (!is_numeric($value))
            return $default;

        return '$' . number_format($value, $decimals, '.', $thousands_sep);
    }
}