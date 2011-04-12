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
 * Kp_Object_Abstract
 *
 * PHP version 5.2
 *
 * LICENSE: This source file is closed source, strictly confidential and
 * proprietary to Integraclick Inc. Viewing the contents of this file binds the
 * viewer to the NDA agreement  available by Integraclick Inc. Electronic
 * transfer of this file outside of the Integraclick corporate network is
 * strictly prohibited. Questions, comments or concerns should be directed to
 * compliance@integraclick.com
 *
 * @category  Abstraction
 * @package   Ic
 * @author    Brandon Kozak <brandon@integraclick.com>
 * @copyright 2010 Integraclick Inc.
 * @license   http://www.integraclick.com Integraclick License
 * @link      http://cpc.clickbooth.com
 */

/**
 * Kp_Object_Abstract base Abstraction object.
 *
 * @category   Abstraction
 * @package    Ic
 * @license    http://www.integraclick.com Integraclick License
 * @link       http://cpc.clickbooth.com
 * @see        ArrayAccess
 * @abstract
 */
abstract class Kp_Object_Abstract implements ArrayAccess, Iterator
{
    /**
     * const NOT_FOUND used for $_data and validation if the key doesn't have a value but is required
     *
     * @var const
     */
    const NOT_FOUND           = "Data not found";

    /**
     * const NULLABLE used for $_data and validation if the value is allowed ot be null or ""
     *
     * @var const
     */
    const NULLABLE            = "Field is NULLABLE";

    /**
     * const HAS_REQUIRED_DATA used for $_data and validation if the key is required and has a value
     *
     * @var const
     */
    const HAS_REQUIRED_DATA   = "Required field has data";

    //TODO: define IS_NEW_RECORD
    const IS_NEW_RECORD       = "Promay key will be set by database.";
     /**
     * protected $_isObjectNew determines if this object is new or was created from some existing data
     *
     * @var bool
     */
    protected $_isObjectNew = true;

    /**
     * protected $_eventPrefix the prefix to use to create and lookup events
     *
     * @var string
     */
    protected $_eventPrefix = 'object';
    
    /**
     * protected $_data holds the object attributes dynamically set and get
     *
     * @var array
     */
    protected $_data = array();

    /**
     * protected $_dirty is used to check if the object is dirty
     *
     * @var array
     */
    protected $_dirty = array();
    
    /**
     * protected $_hasDataChanges is used to check the $_data for changes (true after setData|unsetData call)
     * @var bool
     */
    protected $_hasDataChanges = false;

    /**
    * protected $_origData holds original data that was loaded
    *
    * @var array
    */
    protected $_origData;

    /**
     * protected $_idFieldName holds the name of object id field (most likely based on the model/table object)
     *
     * @var string
     */
    protected $_idFieldName = null;

    /**
     * protected $_underscoreCache setter/getter cache for the underscore method
     *
     * @var array
     */
    protected static $_underscoreCache = array();

    /**
     * protected $_isDeleted object delete flag
     *
     * @var boolean
     */
    protected $_isDeleted = false;

    /**
     * protected $_isValid object is valid flag set after validate()
     *
     * @var boolean
     */
    protected $_isValid = false;

    /**
     * protected $_hasBeenValidated object has been validated flag
     *
     * @var boolean
     */
    protected $_hasBeenValidated = false;

    /**
     * protected $_validationResults hold the results for each $_data element after validation
     *
     * @var array
     */
    protected $_validationResults = array();

    /**
     * protected $_validationResultsResponses hold the result responses for each $_data element after validation
     *
     * @var array
     */
    protected $_validationResultsResponses = array();

    /**
     * protected $_resourceName hold name of the resource to use for $this->getResource() object
     *
     * @var string
     */
    protected $_resourceName = null;

    /**
     * protected $_resource hold the resource object (most likely a Table/Model)
     *
     * @var mixed
     */
    protected $_resource = null;

    // TODO: define $_externals
    protected $_externals = array();

    //TODO: define $_saveResponses
    protected $_saveResponses = array();

    //TODO: define $_externalDataMapper
    protected $_externalDataMapper = array();

    // TODO: define $_log
    protected $_log = array();

    //TODO: define $_isCollection
    protected $_isCollection = false;

    protected $_current = null;

    protected $_keys = array();

    protected function _setupKeys()
    {
        $i = 0;
        $this->_current = $i;
        foreach($this->_data as $key => $value) {
            $position = array_search($key, $this->_keys);
            if($position !== false) {
                if($position == $i) {
                    continue;
                } else {
                    unset($this->_keys[$position]);
                }
            }
            $this->_keys[$i] = $key;
            $i++;
        }
    }

    /**
     * Constructor
     *
     * By default is looking for first argument as array and assignes it as object attributes
     * This behaviour may change in child classes
     *
     * @param array any number of params
     * @see func_get_args
     */
    public function __construct()
    {
        $args = func_get_args();
        if (empty($args[0])) {
            $args[0] = array();
        }
        $this->_data = $args[0];

        $this->init();
    }
    
    // TODO: define resetKeys
    public function resetKeys()
    {
        $this->_keys = array();
    }

    /**
     * public init acts as internal constructor not depended on params. Can be used for object initialization
     */
    public function init()
    {
        if($this->getResourceName() !== null) {
            $this->setResource(Ic::getModel($this->getResourceName()));

            if($this->getResource() !== null) {
                $data = array();
                $columnsMetaData = $this->getResource()->info(Zend_Db_Table::METADATA);

                foreach($columnsMetaData as $columnName=>$columnData) {
                    $data[$columnName] = null;
                    if(isset($columnData['DEFAULT'])) {
                        $type = strtolower(preg_replace('/[^A-Za-z]*/','',$columnData['DATA_TYPE']));
                        switch($type) {
                            case "bit":
                                $value = (bool) intval(str_replace("'", '', ltrim($columnData['DEFAULT'],'b')));
                            break;
                            case "int":
                            case "integer":
                            case "mediumint":
                            case "smallint":
                            case "tinyint":
                            case "bigint":
                                $value = intval($columnData['DEFAULT']);
                            break;
                            case "float":
                            case "double":
                                $value = (double) $columnData['DEFAULT'];
                            break;
                            case "datetime":
                                $value = '';
                            break;
                            case "varchar":
                            case "text":
                            default:
                                $value = $columnData['DEFAULT'];
                            break;
                        }

                        $data[$columnName] = $value;
                    }

                }

                $this->addData($data);
                
                $this->_setupKeys();

                $pk = $this->getResource()->info(Zend_Db_Table::PRIMARY);
                $this->setIdFieldName($pk[1]);

                $this->flagDirty(null, false);
                $this->setDataChanges(false);
                $this->setOrigData();
            }
        }
        return $this;
    }

    /**
     * Set _isDeleted flag value (if $isDeleted param is defined) and return current flag value
     *
     * @param boolean $isDeleted
     * @return boolean
     */
    public function isDeleted($isDeleted = false)
    {
        $result = $this->_isDeleted;
        if (!is_null($isDeleted)) {
            $this->_isDeleted = $isDeleted;
        }
        return $result;
    }

    /**
     * public hasDataChanges get data change status
     *
     * @return bool
     */
    public function hasDataChanges()
    {
        return $this->_hasDataChanges;
    }

    /**
     * public setIdFieldName sets name of object id field
     *
     * @param   string $name
     * @return  mixed  $this
     */
    public function setIdFieldName($name)
    {
        $this->_idFieldName = $name;
        return $this;
    }

    /**
     * public getIdFieldName retrieve name of object id field
     *
     * @param   string $name
     * @return  string $this->_idFieldName
     */
    public function getIdFieldName()
    {
        return $this->_idFieldName;
    }

    /**
     * public getId retrieve object id
     *
     * @return mixed
     */
    public function getId()
    {
        if ($this->getIdFieldName() !== null) {
            return $this->_getData($this->getIdFieldName());
        }
        return $this->_getData('id');
    }

    /**
     * public setId sets object id field value
     *
     * @param   mixed $value
     * @return  mixed $this
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
     * public getClassName get's the class name of the object highly usefull as this is all abstraction we may not know exactly who we are.
     *
     * @return  string $this
     * @see getClassName
     */
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

    protected function _objectLoadedByModel()
    {
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
        if(is_array($key)) {
            $this->_hasDataChanges = true;
            $this->flagDirty(null);
            foreach($key as $k=>$v) {
                $this->_data[$k] = $v;
            }
        } else {
            if(!isset($this->_data[$key])) {
                $this->_hasDataChanges = true;
                $this->flagDirty($key);
            } elseif(isset($this->_data[$key]) && ($this->_data[$key] != $value)) {
                $this->_hasDataChanges = true;
                $this->flagDirty($key);
            } else {
            }

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
            $this->_hasDataChanges = true;
            $this->_data = array();
        } elseif(isset($this->_data[$key])) {
            $this->_hasDataChanges = true;
            unset($this->_data[$key]);
        } else {
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
     * Convert object attributes to string
     *
     * @param  array  $arrAttributes array of required attributes
     * @param  string $valueSeparator
     * @return string
     * /
    public function __toString(array $arrAttributes = array(), $valueSeparator=',')
    {
        $arrData = $this->toArray($arrAttributes);
        return implode($valueSeparator, $arrData);
    }//*/

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

    // TODO: define getExternalMap
    public function getExternalMap($parameter = null)
    {
        if(!is_null($parameter)) {
            return (isset($this->_externalDataMapper[$parameter])) ? $this->_externalDataMapper[$parameter] : null;
        }
        return $this->_externalDataMapper;
    }

    // TODO: define setExternalMap
    public function setExternalMap($parameter, $name) {
        if(!isset($this->_externalDataMapper[$parameter])) {
            $this->_externalDataMapper[$parameter] = array();
        }
        if(!in_array($name, $this->_externalDataMapper[$parameter])) {
            $this->_externalDataMapper[$parameter] = array_merge_recursive(array($name), $this->_externalDataMapper[$parameter]);
        }

        return $this;
    }

    // TODO: define setExternal
    public function setExternal($name, $object)
    {
        $externalData = $object->getData();

        foreach($externalData as $key=>$value) {
            $this->setExternalMap($key, $name);
        }
        $this->_externals[$name] = $object;

        return $this;
    }

    // TODO: define getExternal
    public function getExternal($name)
    {
        return (isset($this->_externals[$name])) ? $this->_externals[$name] : null;
    }

    // TODO: define setDataOnExternal
    public function setDataOnExternal($parameter, $value, $scope = null)
    {
        if($scope == 'all') {
            $externalMap = $this->getExternalMap($parameter);
            
            foreach($externalMap as $class) {
                if($this->getExternal($class) !== null) {
                    $this->getExternal($class)->setData($parameter, $value, 'all');
                }
            }
        } elseif(is_string($scope)) {
            if(strstr($scope,',')) {
                $scope = explode(",",$scope);
                return $this->setDataOnExternal($parameter, $value, $scope);
            }
            
            $this->getExternal($scope)->setData($parameter, $value);
        } elseif(is_array($scope)) {
            foreach($scope as $class) {
                 if($this->getExternal($class) !== null) {
                    $this->getExternal($class)->setData($parameter, $value);
                }
            }
        }

        return $this;
    }

    /**
     * Set/Get attribute wrapper
     *
     * @param   string $method
     * @param   array $args
     * @return  mixed
     */
    // TODO: define __call cases
    public function __call($method, $args = array())
    {
        $methodName = substr($method,3);
        switch (substr($method, 0, 3)) {
            case 'get':
                $key = $this->_underscore($methodName);

                if($this->hasData($key)) {
                    $data = $this->getData($key, isset($args[0]) ? $args[0] : null);
                    self::$_underscoreCache[$methodName] = $key;
                    return $data;
                } else {
                    $resource = $this->getResource();
                    if($resource !== null) {
                        if(method_exists($resource, $method)) {
                            if(is_callable(array($resource, $method))) {
                                return call_user_func_array(array($resource,$method), $args);
                                break;
                            }
                        }
                    }

                    $class = Ic::doUnderscore($methodName);
                    $ext = null;
                    if($this->getExternal($class) === null) {
                        $this->setExternal($class, Ic::get($methodName));
                        
                        $externalId = $this->getExternal($class)->getIdFieldName();
                        if(!empty($args)) {
                            if(is_string($args[0]) || is_array($args[0])) {
                                $ext = $this->getExternal($class)->loadBy($args[0], $args[1]);
                            } else {
                                 $ext = $this->getExternal($class)->loadBy($externalId, $args[0]);
                            }
                        } else {
                            if($this->hasData($externalId)) {
                               $ext = $this->getExternal($class)->loadBy($externalId, array('eq' => $this->getData($externalId)));
                            }
                        }
                    }

                    if(!empty($ext)) {
                        $this->setExternal($class, $ext);
                    }
                    
                    return $this->getExternal($class);
                    break;
                }
                return null;
            break;
            case 'set':
                $key = $this->_underscore($methodName); //date

                $externalClassExists = Ic::exists($methodName, true); //Cpc_Date, Kp_Date true means NO ZEND!
                if(!empty($externalClassExists)) {
                    $this->setExternal($methodName, $args);
                    return $this->getExternal($methodName);
                }

                if(!isset($args[1]) || strtolower($args[1]) == 'all') {
                    $this->setData($key, isset($args[0]) ? $args[0] : null);
                }

                if(isset($args[1])) {
                    $this->setDataOnExternal($key, isset($args[0]) ? $args[0] : null, isset($args[1]) ? $args[1] : null);
                }

                return $this;
            break;
            case 'uns':
                $key = $this->_underscore(substr($method,3));
                $result = $this->unsetData($key);
                return $this;
            break;
            case 'has':
                $key = $this->_underscore(substr($method,3));
                return isset($this->_data[$key]);
            break;
            case "loa":
                $key = substr($methodName,3,(strlen($methodName)-3));
                return $this->loadBy($key, array('eq'=>$args[0]));
            break;
            default:
                if($this->hasData('resource')) {
                    $resource = $this->getResource();
                    if(method_exists($resource, $method)) {
                        if(is_callable(array($resource, $method))) {
                            return call_user_func_array(array($resource,$method), $args);
                        }
                    }
                }
                return null;
            break;
        }
        throw new Kp_Exception("Invalid method ".get_class($this)."::".$method."(".print_r($args,true).")");
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

        $name = str_replace(" ","",$name);

        $result = preg_replace('`(.)([A-Z])([^A-Z]+)`U', '\1_\2\3', $name);
        $result = preg_replace('`([a-z])([A-Z])`U', '\1_\2', $result);
        $result = strtolower($result);
        return $result;
    }

    // TODO: define _camelize
    protected function _camelize($name)
    {
        return ucwords($name);
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
     * Get object loaded data (original data)
     *
     * @param string $key
     * @return mixed
     */
    public function getOrigData($key=null)
    {
        if (is_null($key)) {
            return $this->_origData;
        }
        return isset($this->_origData[$key]) ? $this->_origData[$key] : null;
    }

    /**
     * Initialize object original data
     *
     * @param string $key
     * @param mixed $data
     * @return Kp_Object_Abstract
     */
    public function setOrigData($key=null, $data=null)
    {
        if (is_null($key)) {
            $this->_origData = $this->_data;
        } else {
            $this->_origData[$key] = $data;
        }
        return $this;
    }

    /**
     * Compare object data with original data
     *
     * @param string $field
     * @return boolean
     */
    public function dataHasChangedFor($field)
    {
        $newData = $this->getData($field);
        $origData = $this->getOrigData($field);
        return $newData!=$origData;
    }

    /**
     * Clears data changes status
     *
     * @param boolean $value
     * @return Kp_Object_Abstract
     */
    public function setDataChanges($value)
    {
        $this->_hasDataChanges = (bool)$value;
        return $this;
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


    /**
     * Enter description here...
     *
     * @param string $field
     * @return boolean
     */
    public function isDirty($field=null)
    {
        if (empty($this->_dirty)) {
            return false;
        }
        if (is_null($field)) {
            return true;
        }
        return isset($this->_dirty[$field]);
    }

    /**
     * Enter description here...
     *
     * @param string $field
     * @param boolean $flag
     * @return Kp_Object_Abstract
     */
    public function flagDirty($field, $flag = true)
    {
        if (is_null($field)) {
            foreach ($this->getData() as $field=>$value) {
                $this->flagDirty($field, $flag);
            }
        } else {
            $this->_dirty[$field] = $flag;
        }

        if(!$flag) {
            $this->_hasDataChanges = false;
        }

        return $this;
    }

    // TODO: define clear
    public function clear()
    {
        $this->_data = array();
        $this->_dirty = array();
        $this->_hasDataChanges = false;
        $this->_origData = NULL;
        $this->_idFieldName = null;
        self::$_underscoreCache = array();
        $this->_isDeleted = false;
    }

    // TODO: define reset
    public function reset()
    {
        $orig = $this->getOrigData();
        $this->clear();
        $this->setOrigData(null, $orig);
        return $this;
    }

    // TODO: define hasBeenValidated
    public function hasBeenValidated()
    {
        return $this->_hasBeenValidated;
    }

    // TODO: define isValid
    public function isValid($autoValidate = false, $rollup = false)
    {
        if(!$this->hasBeenValidated()) {
            if($autoValidate) {
                $this->validate($rollup);
            }
        }
        return $this->_isValid;
    }

    // TODO: define getIsObjectNew
    public function getIsObjectNew()
    {
        return $this->_isObjectNew;
    }

     // TODO: define setIsObjectNew
    public function setIsObjectNew($isNew = true)
    {
        $this->_isObjectNew = (bool) $isNew;
    }

    // TODO: define setValidationFor
    public function setValidationFor($key, $valid, $response = "")
    {
        $this->_validationResults[$key] = $valid;
        $this->_validationResultsResponses[$key] = $response;
    }

    // TODO: define load
    public function load($id = 0)
    {
        $this->setIsObjectNew(false);
        
        try {
            $results = $this->getResource()->load($id);
            if(count($results) == 1) {
                $this->addData($results[0]);
                $this->setOrigData();

                $this->flagDirty(null, false);
                $this->setDataChanges(false);
                return $this;
            } elseif(count($results) > 1) {
                return $this->convertToCollection($results);
            } else {
                throw new Kp_Exception('No data found from load');
            }
        } catch(Exception $e) {
            $this->log($e);
            $this->setIsObjectNew(true);
        }
        
        return $this;
    }

    // TODO: define loadBy
    public function loadBy($field, $condition = array()) {
        $this->setIsObjectNew(false);
        if(is_string($field)) {
            $field = $this->_underscore($field);
        }

        try {
            $results = $this->getResource()->loadBy($field, $condition);
            if(count($results) > 1) {
                return $this->convertToCollection($results);
            } elseif(count($results) > 0) {
                $this->addData($results[0]);
                $this->setOrigData();

                $this->flagDirty(null, false);
                $this->setDataChanges(false);
                return $this;
            } else {
                throw new Kp_Exception('No data found from loadBy');
            }
        } catch(Exception $e) {
            $this->log($e);
            $this->setIsObjectNew(true);
        }

        return $this;
    }

    public function loadWith($data = array())
    {
        $this->setData($data);
        return $this->_objectLoadedByModel();
    }

    // TODO: define convertToCollection
    public function convertToCollection($results = array())
    {
        $class = Ic::stripNameSpaces($this->getClassName());
        
        $collection =  Ic::convert(Ic::CONVERT_TO_COLLECTION, $this->getClassName());
        $collection->buildAll($results, $class, false);

        return $collection;
    }

    // TODO: define getSaveResponse
    public function getSaveResponse()
    {
        return $this->_saveResponses;
    }

    // TODO: define setSaveResponse
    public function setSaveResponse($response)
    {
        $this->_saveResponses[] = $response;
    }

    // TODO: define save
    public function save($rollup = false)
    {
        if(!$this->isDirty() && !$this->hasDataChanges()) {
            return $this;
        }

        if(!$this->isValid(true)) {
            throw new Kp_Transaction_Exception('Could not save transaction, data did not validate.');
        }

        try {
            $this->getResource()->save($this);
        } catch(Exception $e) {
            $this->setSaveResponse($e->getMessage());
            return false;
        }
        $this->setSaveResponse($this->getSaveMessage());

        
        if($rollup) {
            $rollupMessages = array();
            foreach($this->_externals as $class=>$external) {
                try {
                    $this->setSaveResponse($class, $external)->save($rollup);
                } catch(Exception $e) {
                    $rollupMessages[] = $e;
                }
            }
            $this->setRollupMessages($rollupMessages);
        }

        return true;
    }

    // TODO: define validate
    public function validate($rollup = false)
    {
        $this->_hasBeenValidated = true;
        $this->_isValid = true;

        $resourceExpectedData = $this->getResource()->info(Zend_Db_Table::METADATA);

        foreach($resourceExpectedData as $columnName=>$columnData) {
            if($columnData['NULLABLE']) {
                if(is_null($this->getData($columnName))) {
                    $this->setData($columnName, 'null');
                    $this->flagDirty($columnName, false);
                }
               
                $this->setValidationFor($columnName, true, Kp_Object_Abstract::NULLABLE);
                continue;
            }

            if(is_null($this->getData($columnName))) {
                if($columnName == $this->getIdFieldName() && $this->getIsObjectNew()) {
                    $this->setValidationFor($columnName, true, Kp_Object_Abstract::IS_NEW_RECORD);
                } else {
                    $this->setValidationFor($columnName, false, Kp_Object_Abstract::NOT_FOUND);
                    $this->_isValid = false;
                }
            } else {
                $this->setValidationFor($columnName, true, Kp_Object_Abstract::HAS_REQUIRED_DATA);
            }
        }

        if($rollup) {
            foreach($this->_externals as $class=>$external) {
                $external->validate($rollup);
            }
        }

        return $this;
    }

    // TODO: define getValidationResults
    public function getValidationResults($showOnlyFailed = false)
    {
        $results = array();
        foreach($this->_validationResults as $key=>$valid) {
            if($showOnlyFailed && !$valid) {
                $results[$key] = $this->_validationResultsResponses[$key];
            } else {
                $results[$key] = $this->_validationResultsResponses[$key];
            }
        }

        return $results;
    }

    // TODO: define delete
    public function delete()
    {
        $this->isDeleted(true);
        $this->getResource()->save($this);
    }

    // TODO: define getResource
    public function getResource()
    {
        return $this->_resource;
    }

    // TODO: define setResource
    public function setResource(Kp_Model_Abstract $model)
    {
        $this->_resource = $model;
    }

     // TODO: define getResourceName
    public function getResourceName()
    {
        return $this->_resourceName;
    }

    // TODO: define setResourceName
    public function setResourceName($name)
    {
        $this->_resourceName = $name;
    }

    // TODO: define log
    public function log(Exception $e, $logLvl = 7)
    {
        $this->_log[] = $e;
        if(Zend_Registry::isRegistered('log')) {
            Zend_Registry::get('log')->log($e->getMessage(), $logLvl);
        }
        return $this;
    }

    // TODO: define isCollection
    public function isCollection()
    {
        return $this->_isCollection;
    }

    /**
     * (PHP 5 &gt;= 5.1.0)<br/>
     * Return the current element
     * @link http://php.net/manual/en/iterator.current.php
     * @return mixed Can return any type.
     */
    public function current () {
        if($this->offsetExists($this->_keys[$this->_current])) {
            return $this->_data[$this->_keys[$this->_current]];
        }

        return null;
    }

    /**
     * (PHP 5 &gt;= 5.1.0)<br/>
     * Move forward to next element
     * @link http://php.net/manual/en/iterator.next.php
     * @return void Any returned value is ignored.
     */
    public function next () {
        $this->_current++;
    }

    /**
     * (PHP 5 &gt;= 5.1.0)<br/>
     * Return the key of the current element
     * @link http://php.net/manual/en/iterator.key.php
     * @return scalar scalar on success, integer
     * 0 on failure.
     */
    public function key () {
        return (isset($this->_keys[$this->_current])) ? $this->_keys[$this->_current] : 0;
    }

    /**
     * (PHP 5 &gt;= 5.1.0)<br/>
     * Checks if current position is valid
     * @link http://php.net/manual/en/iterator.valid.php
     * @return boolean The return value will be casted to boolean and then evaluated.
     * Returns true on success or false on failure.
     */
    public function valid () {
        if(isset($this->_keys[$this->_current])) {
            return true;
        }

        return false;
    }

    /**
     * (PHP 5 &gt;= 5.1.0)<br/>
     * Rewind the Iterator to the first element
     * @link http://php.net/manual/en/iterator.rewind.php
     * @return void Any returned value is ignored.
     */
    public function rewind () {
        $this->_current = 0;
    }

    // TODO: define getKeys
    public function getKeys()
    {
        return $this->_keys;
    }
}
