<?php
// TODO: Define Kp_Db_Adapter_Pdo_Mysql_Abstract
abstract class Kp_Db_Adapter_Pdo_Mysql_Abstract extends Zend_Db_Adapter_Pdo_Mysql {
    /**
     * Current Transaction Level
     *
     * @var int
     */
    protected $_transactionLevel = 0;

    /**
     * Array of callbacks subscribed to commit transaction commit
     *
     * @var array
     */
    static protected $_commitCallbacks = array();
    
    /**
     * Begin new DB transaction for connection
     *
     * @return Varien_Db_Adapter_Pdo_Mysql
     */
    public function beginTransaction()
    {
        if ($this->_transactionLevel===0) {
            parent::beginTransaction();
        }
        $this->_transactionLevel++;
        return $this;
    }

    /**
     * Commit DB transaction
     *
     * @return Varien_Db_Adapter_Pdo_Mysql
     */
    public function commit()
    {
        if ($this->_transactionLevel===1) {
            parent::commit();
        }
        $this->_transactionLevel--;
        return $this;
    }

    /**
     * Rollback DB transaction
     *
     * @return Varien_Db_Adapter_Pdo_Mysql
     */
    public function rollback()
    {
        if ($this->_transactionLevel===1) {
            parent::rollback();
        }
        $this->_transactionLevel--;
        return $this;
    }

    /**
     * Get adapter transaction level state. Return 0 if all transactions are complete
     *
     * @return int
     */
    public function getTransactionLevel()
    {
        return $this->_transactionLevel;
    }

     /**
     * Subscribe some callback to transaction commit
     *
     * @param callback $callback
     * @return Mage_Core_Model_Resource_Abstract
     */
    public function addCommitCallback($callback)
    {
        $adapterKey = spl_object_hash($this);
        self::$_commitCallbacks[$adapterKey][] = $callback;
        return $this;
    }

    /**
     * Convert date to DB format
     *
     * @param   mixed $date
     * @return  string
     */
    public function convertDate($date)
    {
        if ($date instanceof Zend_Date) {
            return $date->toString(self::ISO_DATE_FORMAT);
        }
        return strftime('%Y-%m-%d', strtotime($date));
    }

    /**
     * Convert date and time to DB format
     *
     * @param   mixed $date
     * @return  string
     */
    public function convertDateTime($datetime)
    {
        if ($datetime instanceof Zend_Date) {
            return $datetime->toString(self::ISO_DATETIME_FORMAT);
        }
        return strftime('%Y-%m-%d %H:%M:%S', strtotime($datetime));
    }
}