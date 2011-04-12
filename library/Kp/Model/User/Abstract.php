<?php
// TODO: define Kp_Model_User_Abstract
abstract class Kp_Model_User_Abstract extends Kp_Model_Abstract
{
    // TODO: define $_name
    protected $_name = 'inclick_user';

    // TODO: define init
    public function init() {
        $id = $this->getId();
        //if(empty($id)) {
            $this->setIdFieldName('ID');
        //}

        $primaries = $this->info(Zend_Db_Table::PRIMARY);
        $this->setData('primary_column_name', $primaries[1]);
    }
}
