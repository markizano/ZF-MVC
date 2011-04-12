<?php
/**
 *  Kizano: Zend Framework-frienly library
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
 *  @package    Kizano_Paginator
 *  @copyright  Copyright (c) 2009-2011 Markizano Draconus <markizano@markizano.net>
 *  @license    http://framework.zend.com/license/new-bsd     New BSD License
 *  @author     Markizano Draconus <markizano@markizano.net>
 */

/**
 *  Adapter for handling pagination.
 *
 *  @category   Kizano
 *  @package    Kizano_Paginator
 *  @copyright  Copyright (c) 2009-2011 Markizano Draconus <markizano@markizano.net>
 *  @license    http://framework.zend.com/license/new-bsd     New BSD License
 *  @author     Markizano Draconus <markizano@markizano.net>
 */
class Kizano_Paginator_Adapter_Doctrine implements Zend_Paginator_Adapter_Interface
{
	/**
	 *	Holds an instance of the query that initialized this class.
	 *	
	 *	@var Doctrine_Query
	 */
	protected $_query;

	/**
	 *	The count cache so we don't end up querying the DB 99000011 times >.>
	 *	
	 *	@var Integer
	 */
	protected $_count;

    /**
     * Creates the adapter from a query.
     *
     * @var Doctrine_Query $query
     */
    public function __construct(Doctrine_Query $query)
    {
        $this->_query = $query;
    }

    /**
     * Return the total number of records from the query.
     *
     * @return integer
     */
    public function count()
    {
		if (empty($this->_count) && $this->_count !== 0) {
		    $result = clone $this->_query;
		    $result = $result->select('COUNT(*) count')->fetchOne(array(), Doctrine::HYDRATE_ARRAY);
		    if ($result) {
		        $this->_count = $result['count'];
		    } else {
		        $this->_count = 0;
		    }
	    }

	    return $this->_count;
    }

    /**
     * Retrieve the items for a specified offset.
     *
     * The offset and itemCountPerPage variables are used
     * to determine the page number when constructing the
     * Doctrine_Pager.
     *
     * @param  integer $offset     The offset in the query.
     * @param  integer $maxPerPage The number of items per page.
     * @return Doctrine_Collection
     */
    public function getItems($offset, $maxPerPage)
    {
	    $pager = new Doctrine_Pager($this->_query, 1 + $offset / $maxPerPage, $maxPerPage);
	    return $pager->execute();
    }

	/**
	 *	Gets the current query on the queue.
	 *	
	 *	@return Kizano_Query
	 */
	public function getQuery()
	{
		return $this->_query;
	}

	/**
	 *	Allows you to set the current query object.
	 *	
	 *	@param $query	Kizano_Query		The query to set.
	 *	
	 *	@return Kizano_Paginator_Adapter_Doctrine
	 */
	public function setQuery(Doctrine_Query $query)
	{
		$this->_query = $query;
	}
}

