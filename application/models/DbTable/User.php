<?php
/**
 *  Kizano_Model_DbTable_User
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
 *  @package    DbTable
 *  @copyright  Copyright (c) 2009-2011 Markizano Draconus <markizano@markizano.net>
 *  @license    http://framework.zend.com/license/new-bsd     New BSD License
 *  @author     Markizano Draconus <markizano@markizano.net>
 */

/**
 *  Database accessor to the user table.
 *
 *  @category   Kizano
 *  @package    DbTable
 *  @copyright  Copyright (c) 2009-2011 Markizano Draconus <markizano@markizano.net>
 *  @license    http://framework.zend.com/license/new-bsd     New BSD License
 *  @author     Markizano Draconus <markizano@markizano.net>
 */
class Model_DbTable_User extends Kizano_Db_Table_Abstract
{
    /**
     *  Singleton.
     *  
     *  @var Model_User
     */
    protected static $_instance;

    /**
     *  Tells Zend_Db_Table which table this is interfacing.
     *  
     *  @var string
     */
    protected $_name = 'users';

    /**
     *  Tells Zend_Db_Table which primary key belongs to said table.
     *  
     *  @var string
     */
    protected $_primary = array('user_id');

    /**
     *  Singleton.
     *  
     *  @return void
     */
    public static function getInstance()
    {
        if (empty(self::$_instance)) {
            self::$_instance = new self;
        }

        return self::$_instance;
    }

    /**
     *  ZF-Hook for a construct.
     *  
     *  @return void
     */
    public function init()
    {}
}

