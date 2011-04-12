<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Abstract
 *
 * @author bkozak
 */
abstract class Kp_Transaction_Collection_Abstract extends Kp_Collection_Abstract {
     public function _construct() {
        parent::_construct();
        $this->setResourceModelName('Kp_Model_Transaction_Collection');
    }

    public function clear() {
        parent::clear();
    }

    public function reset()
    {
        parent::setOrigData();
    }
}
