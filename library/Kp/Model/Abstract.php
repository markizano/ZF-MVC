<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Core
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Abstract model class
 *
 * @category    Mage
 * @package     Mage_Core
 * @author      Magento Core Team <core@magentocommerce.com>
 */
abstract class Kp_Model_Abstract extends Zend_Db_Table_Abstract
{
    // TODO: define $_name from Zend_Db_Table_Abstract
    protected $_name = "model";

    // TODO: define $_eventPrefix
    protected $_eventPrefix = 'model';
    
    /**
     * Setter/Getter underscore transformation cache
     *
     * @var array
     */
    protected static $_underscoreCache = array();

    /**
     * Name of object id field
     *
     * @var string
     */
    protected $_idFieldName = null;

     /**
     * Object attributes
     *
     * @var array
     */
    protected $_data = array();

    // TODO: define __construct
    public function  __construct($config = array(), $resourceName = null) {
        if(!is_null($resourceName)) {
            $this->setResourceName($resourceName);
        } else {
            $this->setResourceName("db");
        }
       
        parent::__construct($config);
    }

    // TODO: define init
    public function init()
    {
        $primaries = $this->info(Zend_Db_Table::PRIMARY);
        $this->setData('primary_column_name', $primaries[1]);
        $this->setIdFieldName($this->getPrimaryColumnName());
        
        return $this;
    }
    
    /**
     * Set/Get attribute wrapper
     *
     * @param   string $method
     * @param   array $args
     * @return  mixed
     */
    public function __call($method, $args)
    {
        switch (substr($method, 0, 3)) {
            case 'get' :
                $key = $this->_underscore(substr($method,3));
                $data = $this->getData($key, isset($args[0]) ? $args[0] : null);
                return $data;
            break;
            case 'set' :
                $key = $this->_underscore(substr($method,3));
                $result = $this->setData($key, isset($args[0]) ? $args[0] : null);
                return $result;
            break;
            case 'uns' :
                $key = $this->_underscore(substr($method,3));
                $result = $this->unsetData($key);
                return $result;
            break;
            case 'has' :
                $key = $this->_underscore(substr($method,3));
                return isset($this->_data[$key]);
            break;
            default:
                $resource = $this->getResource();
                if(method_exists($resource, $method)) {
                    if(is_callable(array($resource, $method))) {
                        return call_user_func_array(array($resource,$method), $args);
                    }
                }
                return null;
            break;
        }
        throw new Kp_Exception("Invalid method ".get_class($this)."::".$method."(".print_r($args,1).")");
    }

    /**
     * Attribute getter (deprecated)
     *
     * @param string $var
     * @return mixed
     */

    public function __get($var)
    {
        $var = $this->_underscore($var);
        return $this->getData($var);
    }

    /**
     * Attribute setter (deprecated)
     *
     * @param string $var
     * @param mixed $value
     */
    public function __set($var, $value)
    {
        $var = $this->_underscore($var);
        $this->setData($var, $value);
    }

    /**
     * Get value from _data array without parse key
     *
     * @param   string $key
     * @return  mixed
     */
    protected function _getData($key)
    {
        return isset($this->_data[$key]) ? $this->_data[$key] : null;
    }

    /**
     * Converts field names for setters and geters
     *
     * $this->setMyField($value) === $this->setData('my_field', $value)
     * Uses cache to eliminate unneccessary preg_replace
     *
     * @param string $name
     * @return string
     */
    protected function _underscore($name)
    {
        if (isset(self::$_underscoreCache[$name])) {
            return self::$_underscoreCache[$name];
        }

        $result = preg_replace('`(.)([A-Z])([^A-Z]+)`U', '\1_\2\3', $name);
        $result = preg_replace('`([a-z])([A-Z])`U', '\1_\2', $result);
        $result = strtolower($result);
        self::$_underscoreCache[$name] = $result;
        return $result;
    }

    // TODO: Define _camelize
    protected function _camelize($name)
    {
        return uc_words($name, '');
    }

     /**
     * Get array of objects transfered to default events processing
     *
     * @return array
     */
    protected function _getEventData()
    {
        return array(
            'data_object'       => $this,
            $this->_eventObject => $this,
        );
    }

    /**
     * Processing object before load data
     *
     * @return Kp_Model_Abstract
     */
    protected function _beforeLoad($id, $field = null)
    {
        $params = array('object' => $this, 'field' => $field, 'value'=> $id);
        Ic::dispatchEvent('model_load_before', $params);
        $params = array_merge($params, $this->_getEventData());
        Ic::dispatchEvent($this->_eventPrefix.'_load_before', $params);
        return $this;
    }

    /**
     * Processing object after load data
     *
     * @return Kp_Model_Abstract
     */
    protected function _afterLoad()
    {
        Ic::dispatchEvent('model_load_after', array('object'=>$this));
        Ic::dispatchEvent($this->_eventPrefix.'_load_after', $this->_getEventData());
        return $this;
    }

     /**
     * Processing data save after transaction commit.
     * When method is called we don't have garantee what transaction was really commited
     *
     * @deprecated after 1.4.0.0 - please use afterCommitCallback instead
     * @return Kp_Model_Abstract
     */
    protected function _afterSaveCommit()
    {
        return $this;
    }

    /**
     * Processing object before save data
     *
     * @return Kp_Model_Abstract
     */
    protected function _beforeSave()
    {
        if (!$this->getId() || $this->getId() === null) {
            $this->getIsObjectNew(true);
        }
        Ic::dispatchEvent('model_save_before', array('object'=>$this));
        Ic::dispatchEvent($this->_eventPrefix.'_save_before', $this->_getEventData());
        return $this;
    }

    /**
     * Processing object after save data
     *
     * @return Kp_Model_Abstract
     */
    protected function _afterSave()
    {
        $this->cleanModelCache();
        Ic::dispatchEvent('model_save_after', array('object'=>$this));
        Ic::dispatchEvent($this->_eventPrefix.'_save_after', $this->_getEventData());
        return $this;
    }

     /**
     * Processing object before delete data
     *
     * @return Kp_Model_Abstract
     */
    protected function _beforeDelete()
    {
        Ic::dispatchEvent('model_delete_before', array('object'=>$this));
        Ic::dispatchEvent($this->_eventPrefix.'_delete_before', $this->_getEventData());
        $this->cleanModelCache();
        return $this;
    }

    /**
     * Processing object after delete data
     *
     * @return Kp_Model_Abstract
     */
    protected function _afterDelete()
    {
        Ic::dispatchEvent('model_delete_after', array('object'=>$this));
        Ic::dispatchEvent($this->_eventPrefix.'_delete_after', $this->_getEventData());
        return $this;
    }

    /**
     * Processing manipulation after main transaction commit
     *
     * @return Kp_Model_Abstract
     */
    protected function _afterDeleteCommit()
    {
         Ic::dispatchEvent('model_delete_commit_after', array('object'=>$this));
         Ic::dispatchEvent($this->_eventPrefix.'_delete_commit_after', $this->_getEventData());
         return $this;
    }

     /**
     * Convert object attributes to array
     *
     * @param  array $arrAttributes array of required attributes
     * @return array
     */
    public function __toArray(array $arrAttributes = array())
    {
        if (empty($arrAttributes)) {
            return $this->_data;
        }

        $arrRes = array();
        foreach ($arrAttributes as $attribute) {
            if (isset($this->_data[$attribute])) {
                $arrRes[$attribute] = $this->_data[$attribute];
            }
            else {
                $arrRes[$attribute] = null;
            }
        }
        return $arrRes;
    }

     /**
     * Set required array elements
     *
     * @param   array $arr
     * @param   array $elements
     * @return  array
     */
    protected function _prepareArray(&$arr, array $elements=array())
    {
        foreach ($elements as $element) {
            if (!isset($arr[$element])) {
                $arr[$element] = null;
            }
        }
        return $arr;
    }

    /**
     * Convert object attributes to JSON
     *
     * @param  array $arrAttributes array of required attributes
     * @return string
     */
    protected function __toJson(array $arrAttributes = array())
    {
        $arrData = $this->toArray($arrAttributes);
        $json = Zend_Json::encode($arrData);
        return $json;
    }

    /**
     * Convert object attributes to string
     *
     * @param  array  $arrAttributes array of required attributes
     * @param  string $valueSeparator
     * @return string
     */
    public function __toString(array $arrAttributes = array(), $valueSeparator=',')
    {
        $arrData = $this->toArray($arrAttributes);
        return implode($valueSeparator, $arrData);
    }

    // TODO: define getClassName
    public function getClassName()
    {
        return get_class($this);
    }
    
    /**
     * Add data to the object.
     *
     * Retains previous data in the object.
     *
     * @param array $arr
     * @return Kp_Object_Abstract
     */
    public function addData(array $arr)
    {
        foreach($arr as $index=>$value) {
            $this->setData($index, $value);
        }
        return $this;
    }

    /**
     * Overwrite data in the object.
     *
     * $key can be string or array.
     * If $key is string, the attribute value will be overwritten by $value
     *
     * If $key is an array, it will overwrite all the data in the object.
     *
     * @param string|array $key
     * @param mixed $value
     * @return Kp_Object_Abstract
     */
    public function setData($key, $value=null)
    {
        if(is_array($key) && !is_object($key)) {
            $this->_data = $key;
        } else {
            $this->_data[$key] = $value;
        }
        return $this;
    }

    /**
     * Unset data from the object.
     *
     * $key can be a string only. Array will be ignored.
     *
     * @param string $key
     * @return Kp_Object_Abstract
     */
    public function unsetData($key=null)
    {
        if (is_null($key)) {
            $this->_data = array();
        } else {
            unset($this->_data[$key]);
        }
        return $this;
    }

    /**
     * Retrieves data from the object
     *
     * If $key is empty will return all the data as an array
     * Otherwise it will return value of the attribute specified by $key
     *
     * If $index is specified it will assume that attribute data is an array
     * and retrieve corresponding member.
     *
     * @param string $key
     * @param string|int $index
     * @return mixed
     */
    public function getData($key='', $index=null)
    {
        if (''===$key) {
            return $this->_data;
        }

        $default = null;

        // accept a/b/c as ['a']['b']['c']
        if (strpos($key,'/')) {
            $keyArr = explode('/', $key);
            $data = $this->_data;
            foreach ($keyArr as $i=>$k) {
                if ($k==='') {
                    return $default;
                }
                if (is_array($data)) {
                    if (!isset($data[$k])) {
                        return $default;
                    }
                    $data = $data[$k];
                } elseif ($data instanceof Kp_Object_Abstract) {
                    $data = $data->getData($k);
                } else {
                    return $default;
                }
            }
            return $data;
        }

        // legacy functionality for $index
        if (isset($this->_data[$key])) {
            if (is_null($index)) {
                return $this->_data[$key];
            }

            $value = $this->_data[$key];
            if (is_array($value)) {
                //if (isset($value[$index]) && (!empty($value[$index]) || strlen($value[$index]) > 0)) {
                /**
                 * If we have any data, even if it empty - we should use it, anyway
                 */
                if (isset($value[$index])) {
                    return $value[$index];
                }
                return null;
            } elseif (is_string($value)) {
                $arr = explode("\n", $value);
                return (isset($arr[$index]) && (!empty($arr[$index]) || strlen($arr[$index]) > 0)) ? $arr[$index] : null;
            } elseif ($value instanceof Kp_Object_Abstract) {
                return $value->getData($index);
            }
            return $default;
        }
        return $default;
    }

    /**
     * Set object data with calling setter method
     *
     * @param string $key
     * @param mixed $args
     * @return Kp_Object_Abstract
     */
    public function setDataUsingMethod($key, $args=array())
    {
        $method = 'set'.$this->_camelize($key);
        $this->$method($args);
        return $this;
    }

    /**
     * Get object data by key with calling getter method
     *
     * @param string $key
     * @param mixed $args
     * @return mixed
     */
    public function getDataUsingMethod($key, $args=null)
    {
        $method = 'get'.$this->_camelize($key);
        return $this->$method($args);
    }

    /**
     * Fast get data or set default if value is not available
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function getDataSetDefault($key, $default)
    {
        if (!isset($this->_data[$key])) {
            $this->_data[$key] = $default;
        }
        return $this->_data[$key];
    }

    /**
     * If $key is empty, checks whether there's any data in the object
     * Otherwise checks if the specified attribute is set.
     *
     * @param string $key
     * @return boolean
     */
    public function hasData($key='')
    {
        if (empty($key) || !is_string($key)) {
            return !empty($this->_data);
        }
        return array_key_exists($key, $this->_data);
    }
    
    /**
     * checks whether the object is empty
     *
     * @return boolean
     */
    public function isEmpty()
    {
        if (empty($this->_data)) {
            return true;
        }
        return false;
    }
    
    /**
     * set name of object id field
     *
     * @param   string $name
     * @return  Kp_Object_Abstract
     */
    public function setIdFieldName($name)
    {
        $this->_idFieldName = $name;
        return $this;
    }

    /**
     * Retrieve name of object id field
     *
     * @param   string $name
     * @return  Kp_Object_Abstract
     */
    public function getIdFieldName()
    {
        return $this->_idFieldName;
    }

    /**
     * Retrieve model object identifier
     *
     * @return mixed
     */
    public function getId()
    {
        $fieldName = $this->getIdFieldName();
        if ($fieldName) {
            return $this->_getData($fieldName);
        } else {
            return $this->_getData('id');
        }
    }
    /**
     * Set object id field value
     *
     * @param   mixed $value
     * @return  Kp_Object_Abstract
     */
    public function setId($value)
    {
        if ($this->getIdFieldName()) {
            $this->setData($this->getIdFieldName(), $value);
        } else {
            $this->setData('id', $value);
        }
        return $this;
    }

    /**
     * serialize object attributes
     *
     * @param   array $attributes
     * @param   string $valueSeparator
     * @param   string $fieldSeparator
     * @param   string $quote
     * @return  string
     */
    public function serialize($attributes = array(), $valueSeparator='=', $fieldSeparator=' ', $quote='"')
    {
        $res  = '';
        $data = array();
        if (empty($attributes)) {
            $attributes = array_keys($this->_data);
        }

        foreach ($this->_data as $key => $value) {
            if (in_array($key, $attributes)) {
                $data[] = $key . $valueSeparator . $quote . $value . $quote;
            }
        }
        $res = implode($fieldSeparator, $data);
        return $res;
    }

    /**
     * Present object data as string in debug mode
     *
     * @param mixed $data
     * @param array $objects
     * @return string
     */
    public function debug($data=null, &$objects=array())
    {
        if (is_null($data)) {
            $hash = spl_object_hash($this);
            if (!empty($objects[$hash])) {
                return '*** RECURSION ***';
            }
            $objects[$hash] = true;
            $data = $this->getData();
        }
        $debug = array();
        foreach ($data as $key=>$value) {
            if (is_scalar($value)) {
                $debug[$key] = $value;
            } elseif (is_array($value)) {
                $debug[$key] = $this->debug($value, $objects);
            } elseif ($value instanceof Kp_Object_Abstract) {
                $debug[$key.' ('.get_class($value).')'] = $value->debug(null, $objects);
            }
        }
        return $debug;
    }

    /**
     * Implementation of ArrayAccess::offsetSet()
     *
     * @link http://www.php.net/manual/en/arrayaccess.offsetset.php
     * @param string $offset
     * @param mixed $value
     */
    public function offsetSet($offset, $value)
    {
        $this->_data[$offset] = $value;
    }

    /**
     * Implementation of ArrayAccess::offsetExists()
     *
     * @link http://www.php.net/manual/en/arrayaccess.offsetexists.php
     * @param string $offset
     * @return boolean
     */
    public function offsetExists($offset)
    {
        return isset($this->_data[$offset]);
    }

    /**
     * Implementation of ArrayAccess::offsetUnset()
     *
     * @link http://www.php.net/manual/en/arrayaccess.offsetunset.php
     * @param string $offset
     */
    public function offsetUnset($offset)
    {
        unset($this->_data[$offset]);
    }

    /**
     * Implementation of ArrayAccess::offsetGet()
     *
     * @link http://www.php.net/manual/en/arrayaccess.offsetget.php
     * @param string $offset
     * @return mixed
     */
    public function offsetGet($offset)
    {
        return isset($this->_data[$offset]) ? $this->_data[$offset] : null;
    }


    // TODO: define clear()
    public function clear()
    {
        $this->_data = array();
        $this->_origData;
        $this->_idFieldName = null;
        self::$_underscoreCache = array();
    }

    /**
     * Load object data
     *
     * @param   integer $id
     * @return  Kp_Model_Abstract
     */
    public function load($id = 0)
    {
        $data = array();
        $results = array();
        
        $this->_beforeLoad($id);

        try {
            $results = $this->find($id);
        } catch(Exception $e) {
            $this->setLastStmtError($e->getMessage());
            throw $e;
        }
        $this->_afterLoad();

        $data = $results->toArray();
        return $data;
    }

    // TODO: define _getConditionSql
    protected function _getConditionSql($fieldName, $condition) {
        if (is_array($fieldName)) {
            $orSql = array();
            foreach ($fieldName as $key=>$name) {
                if (isset($condition[$key])) {
                    $orSql[] = '('.$this->_getConditionSql($name, $condition[$key]).')';
                } else {
                    //if nothing passed as condition adding empty condition to avoid sql error
                    $orSql[] = $this->getResource()->quoteInto("$name = ?", '');
                }
            }
            $sql = '('. join(' and ', $orSql) .')';
            return $sql;
        }

        $sql = '';
        //$fieldName = $this->_getConditionFieldName($fieldName);
        if (is_array($condition) && isset($condition['field_expr'])) {
            $fieldName = str_replace('#?', $this->getResource()->quoteIdentifier($fieldName), $condition['field_expr']);
        }
        if (is_array($condition)) {
            if (isset($condition['from']) || isset($condition['to'])) {
                if (isset($condition['from'])) {
                    if (empty($condition['date'])) {
                        if ( empty($condition['datetime'])) {
                            $from = $condition['from'];
                        }
                        else {
                            $from = $this->getResource()->convertDateTime($condition['from']);
                        }
                    }
                    else {
                        $from = $this->getResource()->convertDate($condition['from']);
                    }
                    $sql.= $this->getResource()->quoteInto("$fieldName >= ?", $from);
                }
                if (isset($condition['to'])) {
                    $sql.= empty($sql) ? '' : ' and ';

                    if (empty($condition['date'])) {
                        if ( empty($condition['datetime'])) {
                            $to = $condition['to'];
                        }
                        else {
                            $to = $this->getResource()->convertDateTime($condition['to']);
                        }
                    }
                    else {
                        $to = $this->getResource()->convertDate($condition['to']);
                    }

                    $sql.= $this->getResource()->quoteInto("$fieldName <= ?", $to);
                }
            }
            elseif (isset($condition['eq'])) {
                $sql = $this->getResource()->quoteInto("$fieldName = ?", $condition['eq']);
            }
            elseif (isset($condition['neq'])) {
                $sql = $this->getResource()->quoteInto("$fieldName != ?", $condition['neq']);
            }
            elseif (isset($condition['like'])) {
                $sql = $this->getResource()->quoteInto("$fieldName like ?", $condition['like']);
            }
            elseif (isset($condition['nlike'])) {
                $sql = $this->getResource()->quoteInto("$fieldName not like ?", $condition['nlike']);
            }
            elseif (isset($condition['in'])) {
                $sql = $this->getResource()->quoteInto("$fieldName in (?)", $condition['in']);
            }
            elseif (isset($condition['nin'])) {
                $sql = $this->getResource()->quoteInto("$fieldName not in (?)", $condition['nin']);
            }
            elseif (isset($condition['is'])) {
                $sql = $this->getResource()->quoteInto("$fieldName is ?", $condition['is']);
            }
            elseif (isset($condition['notnull'])) {
                $sql = "$fieldName is NOT NULL";
            }
            elseif (isset($condition['null'])) {
                $sql = "$fieldName is NULL";
            }
            elseif (isset($condition['moreq'])) {
                $sql = $this->getResource()->quoteInto("$fieldName >= ?", $condition['moreq']);
            }
            elseif (isset($condition['gt'])) {
                $sql = $this->getResource()->quoteInto("$fieldName > ?", $condition['gt']);
            }
            elseif (isset($condition['lt'])) {
                $sql = $this->getResource()->quoteInto("$fieldName < ?", $condition['lt']);
            }
            elseif (isset($condition['gteq'])) {
                $sql = $this->getResource()->quoteInto("$fieldName >= ?", $condition['gteq']);
            }
            elseif (isset($condition['lteq'])) {
                $sql = $this->getResource()->quoteInto("$fieldName <= ?", $condition['lteq']);
            }
            elseif (isset($condition['finset'])) {
                $sql = $this->getResource()->quoteInto("find_in_set(?,$fieldName)", $condition['finset']);
            }
            else {
                $orSql = array();
                foreach ($condition as $orCondition) {
                    $orSql[] = "(".$this->_getConditionSql($fieldName, $orCondition).")";
                }
                $sql = "(".join(" and ", $orSql).")";
            }
        } else {
            $sql = $this->getResource()->quoteInto("$fieldName = ?", (string)$condition);
        }
        return $sql;
    }

    // TODO: define loadBy
    public function loadBy($field, $condition = array())
    {
        $data = array();
        $results = array();

        $this->_beforeLoad($field);

        try {
            $where = $this->_getConditionSql($field, $condition);
            $results = $this->fetchAll($where)->toArray();
        } catch(Exception $e) {
            $this->setLastStmtError($e->getMessage());
            throw $e;
        }

        $this->_afterLoad();
        
        return $results;
    }

    // TODO: define getResource()
    public function getResource()
    {
        if(!$this->hasData('resource')) {
            $this->setData('resource', $this->getAdapter()); //Ic::getModel($this->getData('resource_name'))
        }
        return $this->getData('resource');
    }

    /**
     * Object after load processing. Implemented as public interface for supporting objects after load in collections
     *
     * @return Kp_Model_Abstract
     */
    public function afterLoad()
    {
        //$this->getResource()->afterLoad($this);
        //$this->_afterLoad();
        return $this;
    }

    /**
     * Save object data
     *
     * @return Kp_Model_Abstract
     */
    // TODO: refactor this into the save.
    public function save(Kp_Object_Abstract $item)
    {
        $dataCommited = false;
        $data = array();
        $transactionReturn = null;
        
        /**
         * Direct deleted items to delete method
         */
        if ($item->isDeleted()) {
            return $this->delete($item);
        }

        if (!$item->hasDataChanges()) {
            return $this;
        }

        $updated = new Zend_Date(time(), false);
        $item->setUpdated($updated->toString('YYYY-MM-dd HH:mm:ss'));

        $columns = $this->info(Zend_Db_Table::METADATA);
        foreach($columns as $columnname=>$columnData) {
            $data[$columnname] = $item->getData($columnname);
        }

        $this->getResource()->beginTransaction();
        
        try {
            $this->_beforeSave();
           // if ($this->_dataSaveAllowed) {
                if($item->getIsObjectNew()) {
                    if(isset($data[$this->getIdFieldName()])) {
                        unset($data[$this->getIdFieldName()]);
                    }

                    try {
                        $transactionReturn = $this->insert($data);
                         if($transactionReturn === false) {
                            $item->setSaveMessage("Transaction insert failed");
                        } else {
                            $item->setSaveMessage("Transaction created.");
                            $item->setId($transactionReturn);
                        }
                    } catch(Exception $e) {
                        $item->setSaveMessage($e->getMessage());
                    }
   
                } else {
                    $where = $this->getPrimaryColumnName()."=?";
                    $where = $this->getResource()->quoteInto($where, $item->getId());

                    try {
                        $transactionReturn = $this->update($data, $where);
                        if($transactionReturn === false) {
                            $item->setSaveMessage("Transaction update failed");
                        } else {
                            $item->setSaveMessage("Transaction updated.");
                        }
                    } catch(Exception $e) {
                        $item->setSaveMessage($e->getMessage());
                    } 
                }
                $this->_afterSave();
            //}
            $this->getResource()->addCommitCallback(array($this, 'afterCommitCallback'))->commit();
            $dataCommited = true;
        } catch (Exception $e) {
            $this->getResource()->rollBack();
            $item->setSaveMessage($e->getMessage());

            throw $e;
        }

        if ($dataCommited) {
            $this->_afterSaveCommit();
        }

        $item->setSaveStatus($transactionReturn);

        return $item;
    }

    /**
     * Callback function which called after transaction commit in resource model
     *
     * @return Kp_Model_Abstract
     */
    public function afterCommitCallback()
    {
        Ic::dispatchEvent('model_save_commit_after', array('object'=>$this));
        Ic::dispatchEvent($this->_eventPrefix.'_save_commit_after', $this->_getEventData());
        return $this;
    }

    /**
     * Get list of cache tags applied to model object.
     * Return false if cache tags are not supported by model
     *
     * @return array | false
     */
    public function getCacheTags()
    {
        $tags = false;
        if ($this->_cacheTag) {
            if ($this->_cacheTag === true) {
                $tags = array();
            } else {
                if (is_array($this->_cacheTag)) {
                    $tags = $this->_cacheTag;
                } else {
                    $tags = array($this->_cacheTag);
                }
                $idTags = $this->getCacheIdTags();
                if ($idTags) {
                    $tags = array_merge($tags, $idTags);
                }
            }
        }
        return $tags;
    }

    /**
     * Get cahce tags associated with object id
     *
     * @return array
     */
    public function getCacheIdTags()
    {
        $tags = false;
        if ($this->getId() && $this->_cacheTag) {
            $tags = array();
            if (is_array($this->_cacheTag)) {
                foreach ($this->_cacheTag as $_tag) {
                    $tags[] = $_tag.'_'.$this->getId();
                }
            } else {
                $tags[] = $this->_cacheTag.'_'.$this->getId();
            }
        }
        return $tags;
    }

    /**
     * Remove model onject related cache
     *
     * @return Kp_Model_Abstract
     */
    public function cleanModelCache()
    {
        $tags = $this->getCacheTags();
        if ($tags !== false) {
            Zend_Registry::get('cachemanager')->getCache()->cleanCache('all',$tags);
        }
        return $this;
    }
    
    /**
     * Delete object from database
     *
     * @return Kp_Model_Abstract
     */
    public function delete(Kp_Object_Abstract $item)
    {
        $this->getResource()->beginTransaction();
        try {
            $this->_beforeDelete();

            $where = $this->getPrimaryColumnName()."=?";
            $where = $this->getResource()->quoteInto($where, $item->getId());
            
            $deleted = parent::delete($where);
            $this->_afterDelete();

            $this->getResource()->commit();
            $this->_afterDeleteCommit();
        }
        catch (Exception $e){
            $this->getResource()->rollBack();
            throw $e;
        }
        return $this;
    }

    /**
     * Public wrapper for __toArray
     *
     * @param array $arrAttributes
     * @return array
     */
    public function toArray(array $arrAttributes = array())
    {
        return $this->__toArray($arrAttributes);
    }

    /**
     * Public wrapper for __toJson
     *
     * @param array $arrAttributes
     * @return string
     */
    public function toJson(array $arrAttributes = array())
    {
        return $this->__toJson($arrAttributes);
    }

    /**
     * Public wrapper for __toString
     *
     * Will use $format as an template and substitute {{key}} for attributes
     *
     * @param string $format
     * @return string
     */
    public function toString($format='')
    {
        if (empty($format)) {
            $str = implode(', ', $this->getData());
        } else {
            preg_match_all('/\{\{([a-z0-9_]+)\}\}/is', $format, $matches);
            foreach ($matches[1] as $var) {
                $format = str_replace('{{'.$var.'}}', $this->getData($var), $format);
            }
            $str = $format;
        }
        return $str;
    }
}