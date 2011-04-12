<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Abstract
 *
 * @author bkozak
 */
abstract class Kp_FileHandler_Abstract extends Kp_Object_Abstract
{
    public function validate()
    {
        $this->_hasBeenValidated = true;
        $this->_isValid = true;
    }
    /**
     * Factory pattern for loading type classes
     *
     * @param  string $type
     * @param  array  $config
     * @return Kp_FileHandler_Type_Abstract
     */
    public static function factory($type, $config = array())
    {
        $className = 'Kp_FileHandler_Type_' . ucfirst($type);

        /*
         * Load the adapter class.  This throws an exception
         * if the specified class cannot be loaded.
         */
        if (!class_exists($className)) {
            Zend_Loader::loadClass($className);
        }

        $fileClass = new $className($config);

        /*
         * Verify that the object created is a descendent of the abstract adapter type.
         */
        if (! $fileClass instanceof Kp_FileHandler_Type_Abstract) {
            /**
             * @see Kp_FileHandler_Exception
             */
            throw new Kp_FileHandler_Exception("Type class '$className' does not extend Kp_FileHandler_Type_Abstract");
        }

        return $fileClass;
    }
}