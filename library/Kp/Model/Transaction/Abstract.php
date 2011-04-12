<?php
// TODO: define Kp_Model_Transaction_Abstract
abstract class Kp_Model_Transaction_Abstract extends Kp_Model_Abstract
{
    // TODO: define $_name
    protected $_name = 'transaction';

    // TODO: define init
    public function init() {
        $id = $this->getId();
        if(empty($id)) {
            $this->setIdFieldName('transaction_id');
        }

        $primaries = $this->info(Zend_Db_Table::PRIMARY);
        $this->setData('primary_column_name', $primaries[1]);
    }
}
