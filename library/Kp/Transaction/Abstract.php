<?php
/**
 * Description of Abstract
 *
 * @author Brandon Kozak <brandon@integraclick.com>
 */
abstract class Kp_Transaction_Abstract extends Kp_Object_Abstract
{
    public function init() {
        $this->setResourceName('Model_Transaction');
        return parent::init();
    }
}
