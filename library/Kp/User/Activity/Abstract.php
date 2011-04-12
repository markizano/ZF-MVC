<?php
// TODO: define Kp_User_Activity_Log_Manager_Abstract
class Kp_User_Activity_Abstract extends Kp_Object_Abstract
{
    public $LOG_TYPE_CODE_ALL                    = false;
    public $LOG_TYPE_CODE_FAILED_LOGIN           = "failed_login";
    public $LOG_TYPE_CODE_LOGIN                  = "login";
    public $LOG_TYPE_CODE_CREDIT_CARD_DECLINED   = "card_declined";
    public $LOG_TYPE_CODE_CREDIT_CARD_APPROVED   = "card_approval";
    public $LOG_TYPE_CODE_PASSWORD_RESET         = "password_reset";
    
    public function  init() {
        $this->setResourceName('Model_User_Activity');
        parent::init();
    }
}