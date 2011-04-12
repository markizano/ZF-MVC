<?php
// TODO: define Kp_Model_Funding_Method_Abstract
abstract class Kp_Model_Funding_Method_Abstract extends Kp_Model_Abstract
{
    // TODO: define $_name
    protected $_name = 'funding_method';

    // TODO: define init
    public function init() {
        $id = $this->getId();
        if(empty($id)) {
            $this->setIdFieldName('funding_method_id');
        }

        $primaries = $this->info(Zend_Db_Table::PRIMARY);
        $this->setData('primary_column_name', $primaries[1]);
    }
}
