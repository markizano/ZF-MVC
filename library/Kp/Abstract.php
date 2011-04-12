<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Core
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * IC_Abstract
 *
 * PHP version 5.2
 *
 * LICENSE: This source file is closed source, strictly confidential and
 * proprietary to Integraclick Inc. Viewing the contents of this file binds the
 * viewer to the NDA agreement  available by Integraclick Inc. Electronic
 * transfer of this file outside of the Integraclick corporate network is
 * strictly prohibited. Questions, comments or concerns should be directed to
 * compliance@integraclick.com
 *
 * @category  Abstraction
 * @package   Ic
 * @author    Brandon Kozak <brandon@integraclick.com>
 * @copyright 2010 Integraclick Inc.
 * @license   http://www.integraclick.com Integraclick License
 * @link      http://cpc.clickbooth.com
 * @see        Kp_Object_Abstract
 * @abstract
 */

/**
 * Kp_Abstract base static abstraction class mainly used for loading.
 */
abstract class Kp_Abstract extends Kp_Object_Abstract
{
    const TYPE_COLLECTION = "_Collection";
    const TYPE_SINGLE = "";
    const CONVERT_TO_COLLECTION = 'collection';
    const CONVERT_TO_SINGLE = 'single';

    /**
     * public static function load will actually preform the instantation of
     * the object, it is the true factory here.
     *
     * @static
     * @param string $class_name    the name of the class trying to be created.
     * @param string $callingName   Calling name is used as a unique name for and object in the registry.
     * @param array  $args          An array of arguments that will be passed to the constructor if the constructor can recieve params.
     * @param bool   $isNew         Tells the factory to create a new instance of the object or return a previously defined one if it exists.
     * @return mixed Object         Will return the results from the calling object.
     */
    protected static function _load($class_name, $callingName, $args, $isNew) {
        $reflection = new ReflectionClass($class_name);
        if($reflection->isInstantiable()) {
            if(!$isNew) {
                if(!empty($args) && $reflection->getMethod('__construct')->getNumberOfParameters() > 0) {
                    Zend_Registry::set($callingName, $reflection->newInstanceArgs($args));
                } else {
                    Zend_Registry::set($callingName, $reflection->newInstance());
                }
                return Zend_Registry::get($callingName);
            }
            if(!empty($args) && $reflection->getMethod('__construct')->getNumberOfParameters() > 0) {
                return $reflection->newInstanceArgs($args);
            }
            return $reflection->newInstance();
        }
        
        if(!$isNew) {
            Zend_Registry::set($callingName,  call_user_func(array($class_name,"getInstance")));
            if($reflection->getMethod('init')->getNumberOfParameters() > 0 && !empty($args)) {
               return call_user_func_array(array(Zend_Registry::get($callingName), 'init'), $args);
            }
            return Zend_Registry::get($callingName);
        }
        return call_user_func(array($class_name,"getInstance"));
    }

    /**
     * protected static function factory is a wrapper for the _load method
     * factory is only responsible for converting the class name passed,
     * checking the registry for the callingName if exists return it, if not
     * then call exists for the final class name and pass it to load.
     *
     * @static
     * @param string $class_name    the name of the class trying to be created.
     * @param string $callingName   Calling name is used as a unique name for and object in the registry.
     * @param array  $args          An array of arguments that will be passed to the constructor if the constructor can recieve params.
     * @param bool   $isNew         Tells the factory to create a new instance of the object or return a previously defined one if it exists.
     * @return mixed self::_load    Will return the results from the _load method.
     * @see self::_load
     */
    protected static function factory($class_name, $callingName = null, $args = array(), $isNew = false)
    {
        // $args must be an arrayfor call_user_func && call_user_func_array
        if(!is_array($args)) $args = array($args);

        // a class name must be present
        if(empty($class_name)) {
            throw self::throwException("Class was empty, could not create object in base::factory.");
        }

        /**
         * @see self::doUnderscore
         */
        $class_name = self::doUnderscore($class_name);
        /*if(Zend_Registry::isRegistered('log')) {
            Zend_Registry::get('log')->log('Attempting to load: '.$class_name, 0);
        }*/
        if(empty($callingName)) {
            $callingName = $class_name;
        }

        // If were not looking for a new object check to see if it exists in the reigstry via it's callingName before we continue
        if(!$isNew) {
            if(Zend_Registry::isRegistered($callingName)) {
                return Zend_Registry::get($callingName);
            }
        } else {
            $callingName = "";
        }

        $callingName = $final_class_name = self::exists($class_name);


        // If the $final_class_name is still empty at this point we can't find it
        // throw an exception and stop processing
        if(empty($final_class_name)) {
            self::throwException('Class "' . $class_name . '"not found.');
        }

        return self::_load($final_class_name, $callingName, $args, $isNew);
    }

    /**
     * public static function exists iterates through the registered
     * namespaces in the config.ini to locate the class file. If located
     * return the full class name.
     *
     * @param string  $class_name            the name of the class trying to be created.
     * @param bool    $overloadedOnly        An array of arguments that will be passed to the constructor if the constructor can recieve params.
     * @return string $final_class_name      the final class name with added namespace
     */
    public static function exists($class_name, $overloadedOnly = false)
    {
        if(empty($class_name)) {
            return false;
        }
        // Once the factory determines the full name of the class it will be stored here
        $final_class_name = "";

        // By Default Zend_Loader_Autoloader will throw file not found erros
        // Due to the fact that we will always have cases where the files don't
        // exist becuase we are working with multiple name spaces we must set
        // suppressNotFoundWarnings(true) so the errors are suppressed.
        Zend_Loader_Autoloader::getInstance()->suppressNotFoundWarnings(true);

        // Retrieve the namespaces from the application.ini
        $nameSpaces = Zend_Registry::get('application')->getOption('autoloadernamespaces');

        // Loop through each namespace
        foreach($nameSpaces as $ns) {

            // wrap this in a try/catch just in case Zend explodes somewhere
            try {
                // try to autoload with the current namespace ($ns) prepended to the class name passed
                if(Zend_Loader_Autoloader::autoload($ns . "_" . $class_name)) {
                    $final_class_name = $ns . "_" . $class_name;

                    // If we found it then stop looking
                    break;
                }
            } catch(Exception $e) { // catch the exception and log it to the logger
                if(Zend_Registry::isRegistered('log')) {
                     Zend_Registry::get('log')->log("Exception: ".$ns . "_" . $class_name.", ".$e->getMessage(), Zend_Log::DEBUG);
                }
            }

            // reset the  $final_class_name as we want to throw an exceptiomn if it's empty later
            $final_class_name = "";
        }

        // If we were unable to find the class we were looking for with the provided namespaces in the application.ini
        // check to see if Zend has one
        if(empty($final_class_name) && !$overloadedOnly) {
            if(Zend_Loader_Autoloader::autoload("Zend_" . $class_name)) {
                $final_class_name = "Zend_" . $class_name;
            } else {
                // reset the  $final_class_name as we want to throw an exceptiomn if it's empty later
                $final_class_name = "";
            }
        }

        return $final_class_name;
    }

    /**
     * public function __call is a magic method for calling non-existant methods on this object.
     * Ex: Ic::getTransaction(), this will factory for "NAMESPACE"_Transaction
     * Ex: Ic::getModelTransaction(), this will factory for "NAMESPACE"_Model_Transaction
     *
     * @param string $method    the name of the class trying to be created.
     * @param array  $args      An array of arguments that will be passed to the constructor if the constructor can recieve params.
     * @return mixed object     see self::get
     * @see self::get
     */
    public function __call($method, $args = array())
    {
        $method = substr($method,3,strlen($method));
        $method = self::doUnderscore($method);
        return self::get($method, $args);
    }

    /**
     * public function doUnderscore this method converts strings.
     * Ex: getFirstName() gets translated to "first_name" (less the quotes) so it can be used to reference within $this->_data as a key.
     *
     * @static
     * @param string $name      the name to be converted.
     * @return string $result   the converted name.
     */
    public static function doUnderscore($name) {
        if (isset(self::$_underscoreCache[strtolower($name)])) {
            return self::$_underscoreCache[strtolower($name)];
        }
        $name = str_replace('_', '', $name);

        /**
         * Thanks to Joey Mazzarelli <joey@yourlist.com> for a better implementation of conversion
         */
        $result = preg_replace('`(.)([A-Z])([^A-Z]+)`U', '\1_\2\3', $name);
        $result = preg_replace('`([a-z])([A-Z])`U', '\1_\2', $result);

        self::$_underscoreCache[strtolower($name)] = $result;
        return $result;
    }

    /**
     * public static function getModel is a wrapper for the factory and load methods
     * we don't want the developer to have the ability to overload the $isNew parameter
     * if we know from a framework standpoint that we only EVER want 1 instance of and obejct
     * but for some reason we can't make that object a singleton due to inheritace constraints.
     * This way we force the standard way of calling a "model" via the getModel methodm which
     * will explicitly tell the factory method it must return the same instance.
     *
     * @static
     * @param string $class_name    the name of the class trying to be created.
     * @param array  $args          An array of arguments that will be passed to the constructor if the constructor can recieve params.
     * @return mixed object         see self::factory
     * @see self::factory
     */
    public static function getModel($class_name, $arguments = array())
    {
        return self::factory($class_name, $class_name, $arguments, false);
    }

   /**
     * public static function get is a wrapper for the factory and load methods
     * It is the easiest way to get non singleton & model object.
     *
     * @static
     * @param string $class_name    the name of the class trying to be created.
     * @param array  $args          An array of arguments that will be passed to the constructor if the constructor can recieve params.
     * @param string $callingName   Calling name is used as a unique name for and object in the registry.
     * @return mixed object         see self::factory
     * @see self::factory
     */
    public static function get($class_name, $arguments = array(), $callingName = null)
    {
        if(!empty($callingName)) {
            return self::factory($class_name, $callingName, $arguments, false);
        }
        return self::factory($class_name, $callingName, $arguments, true);
    }

    /**
     * Retrieve singleton oject (or psudo-singleton)
     *
     * @static
     * @param   string $class_name  the class name so get
     * @param   mixed $arguments    the arguments to pass to the init() method after creation
     * @return  object
     */
    public static function getSingleton($class_name, $arguments = array())
    {
        return self::factory($class_name, $class_name, $arguments, false);
    }


    /**
     * public static function getEventConfig get's configuration options for events in the application.ini
     *
     * @static
     * @param   string $area        the "area" is just a unique key
     * @param   string $eventName   the name of the event
     * @return  array|false         return the array of the found area/event name or false if none found
     */
    public static function getEventConfig($area, $eventName)
    {
        $opts = Zend_Registry::get("application")->getOptions();

        if(isset($opts[$area][$eventName])) {
            return $opts[$area][$eventName];
        }

        return false;
    }

    /**
     * public static function addEventArea adds and event area to the Registry
     *
     * @static
     * @param   string $area    the "area" is just a unique key
     * @return  void
     */
    public static function addEventArea($area)
    {
        $events = Zend_Registry::get("__Events");
        if (!isset($events[$area])) {
            $events[$area] = array();
        }
        return;
    }

    /**
     * public static function dispatchEvent will call the event(s) registered in an "Area"
     *
     * @static
     * @param   string $eventName    the "area" is just a unique key
     * @param   array  $args         array of args to pass to the called event
     * @return  void
     */
    public static function dispatchEvent($eventName, $args)
    {
        if(!Zend_Registry::isRegistered("__Events")) {
            Zend_Registry::set("__Events", array());
        }

        $events = Zend_Registry::get("__Events");

        foreach ($events as $area=>$events) {
            if (!isset($events[$eventName])) {
                $eventConfig = self::getEventConfig($area, $eventName);
                if (!$eventConfig) {
                    $events[$area][$eventName] = false;
                    continue;
                }
                $observers = array();
                foreach ($events[$area][$eventName]["observers"] as $obsName=>$obsConfig) {
                    $observers[$obsName] = array(
                        "type"   => (string)$obsConfig["type"],
                        "model"  => $obsConfig["class"] ? (string)$obsConfig["class"] : $obsName,
                        "method" => (string)$obsConfig["method"],
                        "args"   => (array)$obsConfig["args"],
                    );
                }
                $events[$eventName]["observers"] = $observers;
                $events[$area][$eventName]["observers"] = $observers;
            }
            if (false===$events[$eventName]) {
                continue;
            } else {
                $event = self::getModel('Event',$args);
                $event->setName($eventName);
                $observer = self::getModel('Event_Observer');
            }

            foreach ($events[$eventName]["observers"] as $obsName=>$obs) {
                $observer->setData(array("event"=>$event));
                switch ($obs["type"]) {
                    case "disabled":
                    break;
                    case "object": case "model":
                        $method = $obs["method"];
                        $observer->addData($args);
                        $event = self::getModel('Event',$obs["model"]);
                        self::callObserverMethod($event, $method, $observer);
                    break;
                    default:
                        $method = $obs["method"];
                        $observer->addData($args);
                        $event = self::getSingleton($obs["model"]);
                        self::callObserverMethod($event, $method, $observer);
                    break;
                }
            }
        }
    }

    /**
     * public static function callObserverMethod will call the moethod sotred in the registry event area
     *
     * @param   object $event       the event object
     * @param   string $method      the moethod to call
     * @param   object $observer    the observer object
     * @return  object|void         returns the results from the call to the method
     */
    public static function callObserverMethod($event, $method, $observer)
    {
        if (method_exists($event, $method)) {
            return $event->$method($observer);
        }
        return;
    }

    /**
     * NOT YET IMPLEMENTED
     *
     * @static
     * @param   arraty $tags
     * @return  array
     */
    public static function cleanCache($tags) {
        // TODO: implement cache cleaning...
        return;
    }

    /**
     * Throw Exception
     *
     * @static
     * @param string $message           the message to throw or log
     * @param string $messageStorage    the name of the storage object
     */
    public static function throwException($message, $messageStorage = null)
    {
        if ($messageStorage && ($storage = self::getSingleton($messageStorage))) {
            $storage->addError($message);
        }
        throw new Kp_Exception($message);
    }

    // TODO: define convert
    public static function convert($type, $name)
    {
        $name = self::stripNameSpaces($name);

        switch(strtolower($type)) {
            case self::CONVERT_TO_COLLECTION:
                $name = $name.self::TYPE_COLLECTION;
                if(self::exists($name)) {
                    return self::get($name);
                } else {
                    return self::get('Collection');
                }
            break;
            case self::CONVERT_TO_SINGLE:
                if(strstr($name, self::TYPE_COLLECTION) !== false) {
                    $name = str_replace(self::TYPE_COLLECTION, '', $name);
                }
  
                return self::get($name);
            break;
        }

        return null;
    }

    // TODO: define stripNameSpaces
    public static function stripNameSpaces($class)
    {
        $nameSpaces = Zend_Registry::get('application')->getOption('autoloadernamespaces');

         foreach($nameSpaces as $ns) {
            if(strstr($class, $ns) !== false) {
                $class = str_replace($ns."_", '', $class);
            }
        }

        return $class;
    }
}