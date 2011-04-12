<?php
// TODO: define Kp_Gateway_Connector_Abstract
abstract class Kp_Gateway_Connector_Abstract extends Kp_Object_Abstract
{
    // TODO: define $_instance
    protected static $_instance = null;

    // TODO: define $_service
    protected static $_service  = null;

    // TODO: define $_subject
    protected static $_subject  = null;

    // TODO: define callSelf
    public function callSelf($method = null, $args = null)
    {
        if(!is_array($args)) {
            $args = array();
        }
        
        if(!is_null($method)) {
            $reflector = new ReflectionClass(self::getInstance());

            $reflectorMethod = $reflector->getMethod($method);

            $hasArgs = $reflectorMethod->getNumberOfParameters();

            if($hasArgs > 0) {
                return $reflectorMethod->invokeArgs(self::$_service, $args);
            } else {
                return $reflectorMethod->invoke(self::$_service);
            }
        }

        return self::getInstance();
    }

    // TODO: define call
    public function call($method = null, $args = null)
    {
        if(!is_array($args)) {
            $args = array($args);
        }
        if(!is_null($method)) {
            $reflector = new ReflectionClass(self::$_service);

            $reflectorMethod = $reflector->getMethod($method);
            if($reflectorMethod && is_callable(array(self::$_service, $method))) {
                $hasArgs = $reflectorMethod->getNumberOfParameters();

                if($hasArgs > 0) {
                    return $reflectorMethod->invokeArgs(self::$_service, $args);
                } else {
                    return $reflectorMethod->invoke(self::$_service);
                }
            }

            return null;
        }

        return self::getInstance();
    }

    // TODO: define getInstance
    public static function getInstance()
    {
       return self::$_instance;
    }

    // TODO: define setService
    public function setService($service = null) {
         if(is_string($service)) {
            self::$_service = Ic::getSingleton($service);
        } elseif(is_object($service) && !($service instanceof Kp_Service_Abstract)) {
            throw new Kp_Exception('Service Object passed was not instance of Kp_Model_Abstract');
        } elseif(is_object($service) && ($service instanceof Kp_Service_Abstract)) {
            self::$_service = $service;
        } else {
            throw new Kp_Gateway_Exception("Service Object could niot be loaded");
        }

        return self::getInstance();
    }

    // TODO: define setSubject
    public function setSubject($subject = null) {
         if(is_string($subject)) {
            self::$_subject = Ic::getModel($subject);
        } elseif(is_object($subject) && !($subject instanceof Kp_Object_Abstract)) {
            throw new Kp_Exception('Service Object passed was not instance of Kp_Model_Abstract');
        } elseif(is_object($subject) && ($subject instanceof Kp_Object_Abstract)) {
            self::$_subject = $subject;
        } else {
            throw new Kp_Gateway_Exception("Subject Object could niot be loaded");
        }

        return self::getInstance();
    }

    // TODO: define init
    public function init($service = null, $subject = null)
    {
        if(!is_null($service)) {
            self::getInstance()->setService($service);
        }

        if(!is_null($subject)) {
            self::getInstance()->setSubject($subject);
        }

        $return = self::getInstance()->call(__METHOD__, array($subject));

        self::getInstance()->callSelf('_afterInit', array($service, $subject));
        self::getInstance()->callSelf('_beforeLoad', array($service, $subject));

        return $return;
    }

    // TODO: define load
    public function load()
    {
        $args = func_get_args();
        if(isset($args[0])) {
            self::getInstance()->setSubject($subject);
            $args = array_shift($args);
        }

        $return = self::getInstance()->call(__METHOD__, $args);

        self::getInstance()->callSelf('_afterLoad', $args);
        self::getInstance()->callSelf('_beforeProcess', $args);

        return $return;
    }

    // TODO: define process
    public function process()
    {
        $args = func_get_args();
        $retrun = self::getInstance()->call(__METHOD__, $args);

        self::getInstance()->callSelf('_afterProcess', $args);
        self::getInstance()->callSelf('_beforeComplete', $args);

        return $return;
    }

    // TODO: define complete
    public function complete()
    {
        $args = func_get_args();

        $return = self::getInstance()->call(__METHOD__, $args);

        self::getInstance()->callSelf('_afterComplete', $args);

        return $return;
    }

    // TODO: define shutdown
    public function shutdown()
    {
        $args = func_get_args();
        return self::getInstance()->call(__METHOD__, $args);

        if(in_array('destruct',$args)) {
            $this->getInstance()->__destruct();
        }
    }

    // TODO: define __destruct
    public function __destruct()
    {
        $args = func_get_args();
        return self::getInstance()->call(__METHOD__, $args);
    }

    // TODO: define _beforeLoad
    public function _beforeLoad()
    {
        $args = func_get_args();
        return self::getInstance()->call(__METHOD__, $args);
    }

    // TODO: define _beforeProcess
    public function _beforeProcess()
    {
        $args = func_get_args();
        self::getInstance()->callSelf('_afterProcess', $args);
        return self::getInstance()->call(__METHOD__, $args);
    }

    // TODO: define _beforeComplete
    public function _beforeComplete()
    {
        $args = func_get_args();
        return self::getInstance()->call(__METHOD__, $args);
    }

    // TODO: define _beforeShutdown
    public function _beforeShutdown()
    {
        $args = func_get_args();
        return self::getInstance()->call(__METHOD__, $args);
    }

    // TODO: define _beforeDestruct
    public function _beforeDestruct()
    {
        $args = func_get_args();
        return self::getInstance()->call(__METHOD__, $args);
    }

    // TODO: define _afterInit
    public function _afterInit()
    {
        $args = func_get_args();
        return self::getInstance()->call(__METHOD__, $args);
    }

    // TODO: define _afterLoad
    public function _afterLoad()
    {
        $args = func_get_args();
        return self::getInstance()->call(__METHOD__, $args);
    }

    // TODO: define _afterComplete
    public function _afterComplete()
    {
        $args = func_get_args();
        return self::getInstance()->call(__METHOD__, $args);
    }

    // TODO: define _afterProcess
    public function _afterProcess()
    {
        $args = func_get_args();
        return self::getInstance()->call(__METHOD__, $args);
    }

    // TODO: define _afterComplete
    public function _afterComplete()
    {
        $args = func_get_args();
        return self::getInstance()->call(__METHOD__, $args);
    }
}
