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
 * @category   Zend
 * @package    Zend_Service
 * @subpackage AuthorizeNet
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Kp_Plugins_Authorizenet_Cim_Response_Xml extends Kp_Plugins_Authorizenet_Cim_Response {
    /**
     * Authorize net XML namespace
     */
    protected $_xmlNamespace = 'AnetApi/xml/v1/schema/AnetApiSchema.xsd';

    /**
     * Checks whether request response object was successful
     *
     * @return  boolean
     */
    public function isSuccessful()
    {
        $xpath = $this->getXPath();
        $result = $xpath->query('//anet:messages/anet:resultCode/text()');
        if((null != $result->item(0))) {
            return (bool)('ok' == strtolower($result->item(0)->nodeValue));
        } else {
            return false;
        }
        
    }

    /**
     * Returns response parsed into SimpleXMLElement object
     * @return  SimpleXMLElement
     */
    public function getParsedData()
    {
        if(null == $this->_parsedData) {
            $this->_parsedData = simplexml_load_string($this->getHttpResponse()->getBody(), null, LIBXML_NOWARNING);
        }
        return $this->_parsedData;
    }
}
