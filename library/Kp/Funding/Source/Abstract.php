<?php
// TODO: define Kp_Funding_Source_Abstract
abstract class Kp_Funding_Source_Abstract extends Kp_Object_Abstract
{
    protected $_isEncrypted = false;
    
    protected function tryConvert()
    {
        if($this->hasData('type')) {
            $type = $this->getType();

            $type = $this->_underscore($type);
            $type = ucfirst($type);

            $class = 'Funding_Source_'.$type;

            $classExists = Ic::exists($class, true);
            if(!empty($classExists)) {
                $obj = Ic::get($class);
                $obj->setData($this->getData());
                $obj->flagDirty(null, false);
            }
        }

        return $obj;
    }

    protected function _objectLoadedByModel() {
        return $this->tryConvert();
    }

    // TODO: define init
    public function init() {
        $this->setResourceName('Model_Funding_Source');
        return parent::init();
    }

    public function load($id = 0) {
        $this->_isEncrypted = true;
        $obj = parent::load($id);
        $this->decrypt();

        //$obj = $this->tryConvert($obj);

        return $obj;
    }

    public function loadBy($field, $condition = array()) {
        $this->_isEncrypted = true;
        $obj = parent::loadBy($field, $condition);
        $this->decrypt();

        //$obj = $this->tryConvert($obj);

        return $obj;
    }

    protected function _getCryptConfigs()
    {
        if(!isset($options['crypt']['iv'])) {
            $options['crypt']['iv'] = '6IUd0#wl+cRIeN_E';
        }

        if(!isset($options['crypt']['key'])) {
            $options['crypt']['key'] = '1oej-es1lachi+spiAspiaX7_@rouc6a';
        }

        if(!isset($options['crypt']['adapter'])) {
            $options['crypt']['adapter'] = 'Mcrypt';
        }

        if(!isset($options['crypt']['algorithm'])) {
            $options['crypt']['algorithm'] = 'cast-256';
        }

        $config['adapter']   = $options['crypt']['adapter'];
        $config['key']       = $options['crypt']['key'];
        $config['algorithm'] = $options['crypt']['algorithm'];

        return $config;
    }

    public function crypt()
    {
        $options = Zend_Registry::get('application')->getOptions();
        
        if(isset($this->_data['data']) && !$this->_isEncrypted) {

            $config = $this->_getCryptConfigs();

            $filter = new Zend_Filter_Encrypt($config);
            $filter->setVector($options['crypt']['iv']);

            $data = serialize($this->_data['data']);
            
            $encrypted = $filter->filter($data);
            
            $this->_data['data'] = $encrypted;

            $this->_isEncrypted = true;
        }

        return $this;
    }

    public function decrypt()
    {
        $options = Zend_Registry::get('application')->getOptions();

        if(isset($this->_data['data']) && $this->_isEncrypted) {

            $config = $this->_getCryptConfigs();
            
            $filter = new Zend_Filter_Decrypt($config);
            $filter->setVector($options['crypt']['iv']);

            $data = $this->_data['data'];

            $decrypted = $filter->filter($data);
            
            $this->_data['data'] = unserialize($decrypted);

            $this->_isEncrypted = false;
        }

        return $this;
    }

    public function save($rollup = false) {
        if(!$this->_isEncrypted) {
            $this->crypt();
        }
        return parent::save($rollup);
    }
}
