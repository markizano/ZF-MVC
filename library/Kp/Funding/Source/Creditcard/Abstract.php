<?php
// TODO: define Kp_Funding_Source_Creditcard_Abstract
abstract class Kp_Funding_Source_Creditcard_Abstract extends Kp_Object_Abstract
{
    protected $_type = 'creditcard';
    
    // TODO: define init
    public function init() {
        $this->setResourceName('Model_Funding_Source_Creditcard');
        return parent::init();
    }

    public function  __set($var, $value) {
        $var = $this->_underscore($var);
        if(!isset($this->_data[$var])) {
            $this->_data['data'][$var] = $value;
        } else {
            parent::__set($var, $value);
        }
    }

    public function  __call($method, $args = array()) {
        $methodName = substr($method,3);
        $key = $this->_underscore($methodName);
        
        if(!isset($this->_data['data']) && !isset($this->_data['data'][$key])) {
            return parent::__call($method, $args);
        }

        return $this->_data['data'][$key];
    }

    public function save($rollup = false) {
        if(!$this->hasData('type') || $this->getType() == "" || $this->getType() == null) {
            $this->setType($this->_type);
        }

        if(!$this->hasData('name') || $this->getType() == "" || $this->getType() == null) {
            $name = $this->getCardNumber();
            $name = substr(strlen($name)-4, 4);
            $this->setName($name);
        }
        return parent::save($rollup);
    }

    public function  validate($rollup = false) {
        parent::validate($rollup);

        $validate = array(
            'Card Type'
            ,'Card Number'
            ,'Card ExperationMonth'
            ,'Card ExperationYear'
            ,'Card Cvn'

            ,'Billing FirstName'
            ,'Billing LastName'
            ,'Billing Street'
            ,'Billing Street2'
            ,'Billing City'
            ,'Billing State'
            ,'Billing PostalCode'
            ,'Billing Country'
            ,'Daily Spending Limit'
        );

        foreach($validate as $name) {
            $_name = strtolower($name);
            $_name = $this->_underscore($_name);

            if(!$this->hasData($_name)) {
                $this->setValidationFor($_name, false, $name.Kp_Object_Abstract::NOT_FOUND);
                $this->_isValid = false;
                continue;
            }

            $this->setValidationFor($_name, true, Kp_Object_Abstract::HAS_REQUIRED_DATA);
        }
    }
}
