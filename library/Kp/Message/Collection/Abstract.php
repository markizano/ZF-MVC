<?php
// TODO: define Kp_Message_Collection_Abstract
class Kp_Message_Collection_Abstract extends Kp_Collection_Abstract
{
    public function count($level = 7, $module = null, $controller = null, $action = null)
    {
        if(!is_null($module)) {
            if(!is_null($controller)) {
                if(!is_null($action)) {
                    if(isset($this->_data[$module][$controller][$action])) {
                        return count($this->_data[$module][$controller][$action]);
                    }
                }

                if(isset($this->_data[$module][$controller])) {
                    return count($this->_data[$module][$controller]);
                }
            }

            if(isset($this->_data[$module])) {
                return count($this->_data[$module]);
            }
        }
        
        return count($this->_data);
    }

    public function getMessagesByLocale($module = null, $controller = null, $action = null)
    {
        if(!is_null($module)) {
            if(!is_null($controller)) {
                if(!is_null($action)) {
                    if(isset($this->_data[$module][$controller][$action])) {
                        return $this->_data[$module][$controller][$action];
                    }
                }

                if(isset($this->_data[$module][$controller])) {
                    return $this->_data[$module][$controller];
                }
            }

            if(isset($this->_data[$module])) {
                return $this->_data[$module];
            }
        }

        return $this->_data;
    }

    public function getMessages($autoLocale = false)
    {
        if($autoLocale) {
             $request = Zend_Controller_Front::getInstance()->getRequest();
             return $this->getMessagesByLocale($request->getModuleName(), $request->getControllerName(),$request->getActionName());
        }
        return $this->_data;
    }

}