<?php
// TODO: define Kp_User_Acl_Control_Abstract
abstract class Kp_User_Acl_Control_Abstract extends Kp_Object_Abstract
{
    // TODO: define init
    public function init() {
        $this->setResourceName('Model_User_Acl_Control');
        return parent::init();
    }
}