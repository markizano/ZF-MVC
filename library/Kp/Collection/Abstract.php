<?php
// TODO: Define Kp_Collection_Abstract
abstract class Kp_Collection_Abstract extends Kp_Object_Abstract implements Iterator {

    // TODO: define init
    public function init() {
        parent::init();
        $this->_isCollection = true;
        $this->resetKeys();
    }

    public function  flagDirty($field, $flag = true, $rollUp = false) {
        if($rollUp) {
            $collection = $this->getCollection();
            foreach($collection as $element) {
                $element->flagDirty($field, $flag);
            }
        }

        return parent::flagDirty($field, $flag);
    }

    // TODO: Define getCollection
    public function getCollection()
    {
        return $this->_data;
    }

    // TODO: Define setCollection
    public function setCollection($data)
    {
        $this->_data = $data;
    }
    
    // TODO: Define addData
    public function addData(array $arr) {
        foreach($arr as $index=>$value) {
            $this->setData($index, $value);
        }
        return $this;
    }

    // TODO: Define setData
    public function setData($key, Kp_Object_Abstract $collectionElement)
    {
        if(!isset($this->_data[$key])) {
            $this->_hasDataChanges = true;
            $this->flagDirty($key);
        } elseif(isset($this->_data[$key]) && ($this->_data[$key] != $collectionElement)) {
            $this->_hasDataChanges = true;
            $this->flagDirty($key);
        } else {
        }

        $this->_data[$key] = $collectionElement;

        return $this;
    }

    // TODO: Define buildAll
    public function buildAll($results = array(), $class = null, $isNew = true)
    {
        foreach($results as $result) {
            $this->build($result, $class, $isNew);
        }

        $this->_setupKeys();
        return $this;
    }

    // TODO: Define build
    public function build($result = array(), $class = null, $isNew = true)
    {
        if(is_null($class)) {
            $collectionElement = Ic::convert(Ic::CONVERT_TO_SINGLE, $this->getClassName())->loadWith($result);
        } else {
            $collectionElement = Ic::get($class)->loadWith($result);
        }

        if(!$isNew) {
            $collectionElement->setDataChanges(false);
            $collectionElement->flagDirty(null, false);
            $collectionElement->setIsObjectNew(false);
        }

        $id = $collectionElement->getId();

        if(!$id) {
            $id = $this->count();
        }
        $this->setData($id, $collectionElement);

        return $this;
    }

    //TODO: define getData
    public function getData($index = '', $key = '') {
        if(!empty($index)) {
            if(isset($this->_data[$index])) {
                if(!empty($key)) {
                    return $this->_data[$index]->getData($key);
                }

                return $this->_data[$index];
            }
        }

        return $this->getCollection();
    }

    public function __toArray(array $arrAttributes = array())
    {
        if (empty($arrAttributes)) {
            foreach($this->_data as $key=>$element) {
                $returnArray[$key] = $element->toArray();
            }

            return $returnArray;
        }

        $arrRes = array();
        foreach ($arrAttributes as $attribute) {
            if (isset($this->_data[$attribute])) {
                $arrRes[$attribute] = $this->_data[$attribute];
            }
            else {
                $arrRes[$attribute] = null;
            }
        }
        return $arrRes;
    }
    
    public function getDataAsArray($key)
    {
        $returnArray = array();
        
        foreach($this->_data as $id=>$element) {
            if($element->offsetExists($key)) {
                $returnArray[] = $element->getData($key);
            }
        }

        return $returnArray;
    }

    public function count()
    {
        return count($this->getCollection());
    }
 }
