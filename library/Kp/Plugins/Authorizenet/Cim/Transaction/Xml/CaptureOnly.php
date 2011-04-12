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
 * @package    Zend_Service
 * @subpackage AuthorizeNet
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

/**
 * This transaction type is used to complete a previously authorized transaction
 * that was not originally submitted through the payment gateway or that requires
 * voice authorization.
 *
 * @category   Zend
 * @package    Zend_Service
 * @subpackage AuthorizeNet
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Kp_Plugins_Authorizenet_Cim_Transaction_Xml_CaptureOnly extends Kp_Plugins_Authorizenet_Cim_Transaction_CaptureOnly
{
    /**
     * __toString cannot be used as rvalue at this time - so this hack
     *
     * @param   int $indent How many spaces to indent the content with
     * @return  string
     */
    public function toString($indent = 0)
    {
        return $this->getTagValue('profileTransCaptureOnly', parent::toString($indent + 1), $indent, true);
    }

}