<?php
/**
 *  Kizano_Model_User
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
 *  @package    Model
 *  @copyright  Copyright (c) 2009-2011 Markizano Draconus <markizano@markizano.net>
 *  @license    http://framework.zend.com/license/new-bsd     New BSD License
 *  @author     Markizano Draconus <markizano@markizano.net>
 */

/**
 *  User model for interfacing with a user.
 *
 *  @category   Kizano
 *  @package    Model
 *  @copyright  Copyright (c) 2009-2011 Markizano Draconus <markizano@markizano.net>
 *  @license    http://framework.zend.com/license/new-bsd     New BSD License
 *  @author     Markizano Draconus <markizano@markizano.net>
 */
class Model_User extends Kizano_Db_Model
{
    /**
     *  Lists out the valid user roles for the ACL.
     */
    const TYPE_GUEST = "G";
    const TYPE_ADMIN = "A";

    /**
     *  Quick Method to obtain an authorized user.
     *  
     *  @return Array
     */
    public static function getAuthUser()
    {
        return array(
            'user_id' => 1,
            'username' => 'test-user',
            'email' => 'test@example.com',
            'type' => self::TYPE_GUEST,
        );
    }

    /**
     *  Bootstraps this user model.
     *  
     *  @return void
     */
    public function __construct()
    {
        $this->_table = Model_DbTable_User::getInstance();
    }

    /**
     *  Fetches the user information from the DB based on a given `user_id`.
     *  
     *  @param Integer  $user_id    The `user_id` to match.
     *  
     *  @return Array
     */
    public function getUser($user_id)
    {
        if (empty($user_id) || !is_numeric($user_id)) {
            throw new InvalidArgumentException('Argument 1 ($user_id) expected to ' .
                'be numeric and not empty.');
        }

        $user = $this->_table->find(array($user_id))->toArray();
        if ($user) {
            return array_shift($user);
        } else {
            return array();
        }
    }
}

