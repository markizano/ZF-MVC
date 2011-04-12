<?php
// TODO: Define Kp_Db_Adapter_Pdo_Mysql
class Kp_Db_Adapter_Pdo_Mysql extends Kp_Db_Adapter_Pdo_Mysql_Abstract {
    /**
     * Commit resource transaction
     *
     * @return Mage_Core_Model_Resource_Abstract
     */
    public function _commit()
    {
        parent::_commit();
        /**
         * Process after commit callbacks
         */
        //if ($this->getTransactionLevel() === 0) {
            $adapterKey = spl_object_hash($this);
            if (isset(self::$_commitCallbacks[$adapterKey])) {
                $callbacks = self::$_commitCallbacks[$adapterKey];
                self::$_commitCallbacks[$adapterKey] = array();
                foreach ($callbacks as $index => $callback) {
                    call_user_func($callback);
                }
            }
        //}
        return $this;
    }
}