<?php
// TODO: define Kp_Funding_Method_Abstract
abstract class Kp_Funding_Method_Abstract extends Kp_Object_Abstract
{
    // TODO: define $_gateway
    protected $_gateway = null;

    // TODO: define init
    public function init() {
        $this->setResourceName('Model_Funding_Method');
        return parent::init();
    }
}
