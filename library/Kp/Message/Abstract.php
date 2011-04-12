<?php
// TODO: define Kp_Message_Abstract
class Kp_Message_Abstract extends Kp_Object_Abstract {
    public static $EMERG   = 0;  // Emergency: system is unusable
    public static $ALERT   = 1;  // Alert: action must be taken immediately
    public static $CRIT    = 2;  // Critical: critical conditions
    public static $ERR     = 3;  // Error: error conditions
    public static $WARN    = 4;  // Warning: warning conditions
    public static $NOTICE  = 5;  // Notice: normal but significant condition
    public static $INFO    = 6;  // Informational: informational messages
    public static $DEBUG   = 7;  // Debug: debug messages

    // TODO: define init
    public function  init() {
        parent::init();
    }

    public function setMessage($message, $messageType = 4, $request = null)
    {
        $this->setData('message', $message);
        $this->setData('message_type',$messageType);
        $this->setData('created', date('m/d/Y G:i:s',time()));
        if(is_null($request)) {
            $request = Zend_Controller_Front::getInstance()->getRequest();
        }

        $this->setData('module', $request->getModule());
        $this->setData('controller', $request->getController());
        $this->setData('action', $request->getAction());
    }
}