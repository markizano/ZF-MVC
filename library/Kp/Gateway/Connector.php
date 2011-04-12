<?php
// TODO: define Kp_Gateway_Connector
class Kp_Gateway_Connector extends Kp_Gateway_Connector_Abstract
{
    // TODO: define getInstance
    public static function getInstance()
    {
        if(is_null(self::$_instance))
        {
            self::$_instance = new self();
        }

        return parent::getInstance();
    }
}
