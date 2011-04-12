<?php
/**
 *
 * @category   Cb
 * @package    Cb_Controller
 * @subpackage Plugins
 * @author     James Solomon <james@integraclick.com>
 */
// TODO: Define Kp_Controller_Plugin_Ajax
class Kp_Controller_Plugin_Ajax extends Zend_Controller_Plugin_Abstract
{
    // TODO: Define __construct
    public function  __construct() {
    }

    // TODO: Define dispatchLoopStartup
    public function dispatchLoopStartup(Zend_Controller_Request_Abstract $request)
    {
        $header = $request->getHeader('Accept');

        if ($request->isXmlHttpRequest()) {
            switch (true) {
                case (strstr($header, 'application/json')):
                    $request->setParam('format', 'json');
                    break;

                case (strstr($header, 'application/xml')):
                    $request->setParam('format', 'xml');
                    break;

                case (strstr($header, 'text/html')):
                    $request->setParam('format', 'html');
                    break;
            }
        }
    }
}