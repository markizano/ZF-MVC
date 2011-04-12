<?php
/**
 *  Kizano_Doctrine_Table
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
 *  @package    Doctrine
 *  @copyright  Copyright (c) 2009-2011 Markizano Draconus <markizano@markizano.net>
 *  @license    http://framework.zend.com/license/new-bsd     New BSD License
 *  @author     Markizano Draconus <markizano@markizano.net>
 */

/**
 *  Extension to Doctrine's table class to enhance functionality.
 *
 *  @category   Kizano
 *  @package    Doctrine
 *  @copyright  Copyright (c) 2009-2011 Markizano Draconus <markizano@markizano.net>
 *  @license    http://framework.zend.com/license/new-bsd     New BSD License
 *  @author     Markizano Draconus <markizano@markizano.net>
 */
class Kizano_Doctrine_Table extends Doctrine_Table
{
    /**
     * Creates a query on this table. Overrides Doctrine's default Doctrine_Query class.
     *
     * This method returns a new Doctrine_Query object and adds the component
     * name of this table as the query 'from' part.
     * <code>
     * $table = Doctrine_Core::getTable('User');
     * $table->createQuery('myuser')
     *       ->where('myuser.Phonenumber = ?', '5551234');
     * </code>
     *
     * @param string    $alias  Name for component aliasing
     *
     * @return Doctrine_Query
     */
    public function createQuery($alias = '')
    {
        if ( ! empty($alias)) {
            $alias = ' ' . trim($alias);
        }

        $class = $this->getAttribute(Doctrine_Core::ATTR_QUERY_CLASS);

        return Kizano_Query::create($this->_conn, $class)
            ->from($this->getComponentName() . $alias);
    }

    /**
     * Find records basing on a field.
     *
     * @patch Implements query result caching.
     *
     * @param string $column            field for the WHERE clause
     * @param string $value             prepared statement parameter
     * @param int $hydrationMode        Doctrine_Core::HYDRATE_ARRAY or Doctrine_Core::HYDRATE_RECORD
     * @return Doctrine_Collection|array
     */
    public function findByCache($fieldName, $value, $hydrationMode = null)
    {
        return $this->createQuery('dctrn_find')
            ->where($this->buildFindByWhere($fieldName), (array) $value)
            ->executeCache(array(), $hydrationMode);
    }

    /**
     * Finds the first record that satisfy the clause.
     *
     * @patch Implements query result caching.
     *
     * @param string $column            field for the WHERE clause
     * @param string $value             prepared statement parameter
     * @param int $hydrationMode        Doctrine_Core::HYDRATE_ARRAY or Doctrine_Core::HYDRATE_RECORD
     * @return Doctrine_Record
     */
    public function findOneByCache($fieldName, $value, $hydrationMode = null)
    {
        return $this->createQuery('dctrn_find')
            ->where($this->buildFindByWhere($fieldName), (array) $value)
            ->limit(1)
            ->fetchOneCache(array(), $hydrationMode);
    }
}

