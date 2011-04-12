<?php
// TODO: Define Kp_Acl_Abstract
abstract class Kp_Acl_Abstract extends Kp_Object_Abstract
{
    // TODO: Define init
    public function init() {
        $this->setResourceName('Model_Acl');
        return parent::init();
    }

}
