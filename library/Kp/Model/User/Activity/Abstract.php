<?php
// TODO: define Model_Model_User_Activity_Abstract
class Kp_Model_User_Activity_Abstract extends Kp_Model_Abstract
{   
    // TODO: define $_name
    protected $_name = 'inclick_account_activity_log';

    // TODO: define init
    public function init() {
        $id = $this->getId();
        if(empty($id)) {
            $this->setIdFieldName('account_activity_log_id');
        }

        $primaries = $this->info(Zend_Db_Table::PRIMARY);

        $this->setData('primary_column_name', $primaries[1]);
    }
}
