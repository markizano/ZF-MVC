<?php
// TODO: define Kp_Model_Collection_Abstract
// TODO: determine if needed
abstract class Kp_Model_Collection_Abstract extends Kp_Model_Abstract
{
    // TODO: define $_collection
    abstract protected $_collection = array();

    // TODO: define getCollection
    public function getCollection()
    {
        return $this->_collection;
    }

    // TODO: define setCollection
    public function setCollection(Kp_Collection_Abstract $collection)
    {
        $this->_collection = $collection;
    }
}
