<?php
/**
 *  Kizano_Db_Model
 *
 *  LICENSE
 *
 *  This source file is subject to the new BSD license that is bundled
 *  with this package in the file LICENSE.txt.
 *  It is also available through the world-wide-web at this URL:
 *  http://framework.zend.com/license/new-bsd
 *  If you did not receive a copy of the license and are unable to
 *  obtain it through the world-wide-web, please send an email
 *  to license@zend.com so we can send you a copy immediately.
 *
 *  @category   Kizano
 *  @package    Db
 *  @copyright  Copyright (c) 2009-2011 Markizano Draconus <markizano@markizano.net>
 *  @license    http://framework.zend.com/license/new-bsd     New BSD License
 *  @author     Markizano Draconus <markizano@markizano.net>
 */

/**
 *  Generic model abstract for handling models that map to tables.
 *
 *  @category   Kizano
 *  @package    Db
 *  @copyright  Copyright (c) 2009-2011 Markizano Draconus <markizano@markizano.net>
 *  @license    http://framework.zend.com/license/new-bsd     New BSD License
 *  @author     Markizano Draconus <markizano@markizano.net>
 */
abstract class Kizano_Db_Model
{
    /**
     *  Holds an instance of the table this model is influencing.
     *  
     *  @var Zend_Db_Table_Abstract
     */
    protected $_table;

    /**
     *  Gets the current table this model is influencing.
     *  
     *  @return Zend_Db_Table_Abstract
     */
    public function getTable()
    {
        return $this->_table;
    }

    /**
     *  Sets the table this model should influence.
     *  
     *  @return Kizano_Db_Model
     */
    public function setTable(Zend_Db_Table_Abstract $table)
    {
        if (!$table instanceof Zend_Db_Table_Abstract) {
            throw new InvalidArgumentException('Argument 1 ($table) expected instance of Zend_Db_Table_Abstract');
        }
        $this->_table = $table;
        return $this;
    }
}

