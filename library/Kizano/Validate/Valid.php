<?php
/**
 *  Kizano_Validate_Valid
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
 *  @package    Validate
 *  @copyright  Copyright (c) 2009-2011 Markizano Draconus <markizano@markizano.net>
 *  @license    http://framework.zend.com/license/new-bsd     New BSD License
 *  @author     Markizano Draconus <markizano@markizano.net>
 */

/**
 *  A misc. collection of validation methods I've created over the years.
 *
 *  @category   Kizano
 *  @package    Validate
 *  @copyright  Copyright (c) 2009-2011 Markizano Draconus <markizano@markizano.net>
 *  @license    http://framework.zend.com/license/new-bsd     New BSD License
 *  @author     Markizano Draconus <markizano@markizano.net>
 */
class Kizano_Validate_Valid
{

    /**
     *    Validates an eMail against a regular expression. Credits to Dionyziz@irc.freenode.net#PHP
     *  
     *    @param email    String    The eMail address to validate.
     *  
     *    @return            Mixed    $email on Valid; FALSE On Fail/Error
     */
    public static function eMail($email)
    {
        return preg_match(
            '/^[a-z0-9%_+.-]+@'.
            '(([a-z0-9][a-z0-9-]{0,62}(?<!-)\.)*'.
            '([a-z]{2,4}|museum)|(([0-9]|[1-9][0-9]|'.
            '(1[0-9][0-9]|2([0-4][0-9]|5[0-5])))\.) {3}'.
            '([0-9]|[1-9][0-9]|(1[0-9][0-9]|2([0-4][0-9]|5[0-5])))'.
            '(?<!0\.0\.0\.0))$/i',
            $email
        )? $email: false;
    }

    /**
     *    Checks to make sure the deposited string is indeed an IPv4 Address
     *  
     *    @param IP        String. The value to validate as IP
     *  
     *    @return            Boolean. True if VALID IP; False if INVALID IP
     */
    public static function is_IP($IP)
    {
        return (bool)preg_match(
            '/(([0-9]|[1-9][0-9]|(1[0-9][0-9]|2([0-4][0-9]|5[0-5])))\.) {3}'.
            '([0-9]|[1-9][0-9]|(1[0-9][0-9]|2([0-4][0-9]|5[0-5])))(?<!0\.0\.0\.0)/',
            $IP
        );
    }

    /**
     *    Verifies a string contains only hexadecimal characters
     *  
     *    @param Hex        String.        The HEX string to test
     *    @param len        Int.        The number of characters to verify. Can be a range if DELIMIT ","
     *  
     *    @return         Mixed.        $Hex; FALSE if NOT HEX
     */
    public static function Hex($Hex, $len = 0)
    {
        return (bool)preg_match('/[a-fA-F0-9]'.($len == 0? '*': "{{$len}}").'/i', $Hex)? $Hex: false;
    }

    /**
     *    Sanitizes Input for output
     *    @old-url            http://pastie.org/621895
     *    @version            2.4
     *    @param Data            String        The data to sanitize
     *    @param link            Resource    The MySQL Resource to use for mysql_real_escape_string()
     *    @param Length        Int            The length of the data to return
     *    @param FilterType    Int            The FILTER_VAR constant filter type to use
     *                                    One of:
     *            FILTER_SANITIZE_STRING    - Validates the input as a string
     *            FILTER_VALIDATE_INT        - Validates the input as an integer (Finds digits 0-9)
     *            FILTER_VALIDATE_FLOAT    - Validates the input as a floating-point integer (Finds digits 0-9 and decimals["."])
     *            FITLER_VALIDATE_NUMBER    - Validates the input as a number (Finds digits 0-9, decimals["."], and delimiters[","])
     *            FILTER_VALIDATE_EMAIL    - Validates the input as a valid input
     *    @param Transform    Boolean        TRUE to change characters to HTML Entities
     *                                    FALSE to remove disallowed characters
     *    @return             Mixed        A clean version of $Data if it validates properly.
     *                                    FALSE if validation fails.
     */
    const ALLOWED_HTML = '<p><br><em><strong><b><i><u><span><a>';
    const FILTER_VALIDATE_HEX = 40;
    const FILTER_VALIDATE_NUMBER = 100;
    const FILTER_VALIDATE_COMPARE = 120;
    const FILTER_VALIDATE_ALPHANUMBER = 130;
    const FILTER_VALIDATE_ALPHA = 140;
    public static function Sanitize($Data, $link = null, $Length = 0, $FilterType = FILTER_SANITIZE_STRING, $Transform = false)
    {
        if (function_exists('filter_var')) {
            switch ($FilterType) {
                case self::FILTER_VALIDATE_HEX:
                    $result = self::hex($Data, $Length);
                    break;
                case self::FILTER_VALIDATE_NUMBER:
                    $result = preg_match('/^([0-9\.]+)$/', $Data) != false? (double)$Data: false;
                    break;
                case self::FILTER_VALIDATE_COMPARE:
                    $result = preg_match('/^\s?(=|\<=|\>=|\<|\>|==|\!=)\s?$/', $Data) != false? $Data: false;
                    break;
                case self::FILTER_VALIDATE_ALPHANUMBER:
                    $result = preg_match('/^([A-Za-z0-9\.\, ]+)$/i', $Data) != false? (string)$Data: false;
                    break;
                case self::FILTER_VALIDATE_ALPHA:
                    $result = preg_match('/^([A-Za-z]+)$/i', $Data) != false? (string)$Data: false;
                    break;
                default:
                    $result = filter_var($Data, $FilterType);
            }
        } else {
            switch ($FilterType) {
                case FILTER_SANITIZE_STRING:
                    $result = preg_match('/^([A-Za-z0-9_\.\,\!\$\#\@\\\/\%\^\s=\+:;]+)$/i', $Data) != false? (string)$Data: 0;
                    break;
                case FILTER_VALIDATE_INT:
                    $result = preg_match('/^[0-9]*$/', $Data) != false? (int)$Data: false;
                    break;
                case FILTER_VALIDATE_EMAIL:
                    $result = valideMail($Data);
                    break;
                case FILTER_VALIDATE_HEX:
                    $result = is_hex($Data, $Length);
                    break;
                case FILTER_VALIDATE_FLOAT:
                    $result = preg_match('/^[0-9\\.]*$/', $Data) != false? (float)$Data: false;
                    break;
                case FILTER_VALIDATE_NUMBER:
                    $result = preg_match('/^[0-9\\.]*$/', $Data) != false? (double)$Data: false;
                    break;
                case FILTER_VALDIATE_COMPARE:
                    $result = preg_match('/^\s?(=|\<=|\>=|\<|\>|==|\!=)\s?$/', $Data) != false? (string)$Data: false;
                    break;
                case FILTER_VALIDATE_ALPHANUMBER:
                    $result = preg_match('/^([A-Za-z0-9\\.\\, ]+)$/i', $Data) != false? (string)$Data: false;
                    break;
                case FILTER_VALIDATE_ALPHA:
                    $result = preg_match('/^([A-Za-z]+)$/i', $Data) != false? (string)$Data: false;
                    break;
                default:
                    $result = $Data;
            }
        }
        if ($Length > 0)
            $result = SubStr($result, 0, $Length);
        if (check_inject($result)) return false;
        $result = stripslashes($result);
        $result = strip_tags($result, Allowed_HTML);
        if ($Transform)
            $result = htmlspecialchars($result, ENT_QUOTES, 'UTF-8');
        if (isset($link) && $link != null)
            $result = mysql_real_escape_string($result, $link);
        return (string)$result;
    }

    /**
     *    Checks for possible SQL Injections. Credits to GaintSura (Chris Walker) torncomputersinc@gmail.com
     *    @param check    Array(). Contains data to be checked for SQL Injections.
     *                    NOTE: Best to use $_COOKIE, $_POST and $_GET directly.
     *    @return            TRUE On INFECTED; We found an Exploit!
     *                    FALSE On CLEAN; The data is ok to pass to the database
     * /
    public static function check_inject(array $check)
    {
        foreach ($check as $key => $val) {              // Go thru each check value
            if (is_array($val)) {                       // If it is an array
                $check['array' . $key] = '';            // Initialize the array holder
                foreach ($val as $vKey => $value) {     // Go thru each array's component
                    $check['array' . $key] .= $value;   // Append the value to the 
                }

                unset($check[$key]);                    // Get rid of the array
            }
        }

        $meta = preg_grep('/(\=|;|\'|"|--)*(%27|%23|%3B|%6F|%4F|%72|%52|%3D)*^(\d\d\d\d\d)*^(\d\d\d\d\d\d\d\d\d\d)+[(%(\d\s))]+[????(2-7)]/i', $check);

        if (count($meta) >= 1) {
            $f = fOpen(ini_get('error_log'), 'wb');
            fWrite($f, 
                date('Y-m-d/H:i:s').' - SQL Injection Detected from address '."$_SERVER[REMOTE_ADDR]\n".
                "\tUserAgent: $_SERVER[HTTP_USER_AGENT]\n".
                "\tRequestURI: $_SERVER[REQUREST_URI]\n".
                "\tQueryString: $_SERVER[QUERY_STRING]\n"
            );
            fClose($f);
            return true;
        } else {
            return false;
        }

        return true;
    } //*/
}

