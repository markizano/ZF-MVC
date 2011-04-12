<?php
/**
 *  Kizano_Doctrine_Query
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
 *  Extension to the Doctrine_Query to provide override support for the queries.
 *
 *  @category   Kizano
 *  @package    Doctrine
 *  @copyright  Copyright (c) 2009-2011 Markizano Draconus <markizano@markizano.net>
 *  @license    http://framework.zend.com/license/new-bsd     New BSD License
 *  @author     Markizano Draconus <markizano@markizano.net>
 */
class Kizano_Doctrine_Query extends Doctrine_Query
{
	/**
	 *	Implements result query caching. PDO can't be serialized, so we perform the
	 *	caching after the result of the query, and ensure it's not Doctrine::HYDRATE_RECORD
	 *	
	 *	@param Array    $params     Parameters that Doctrine uses?
	 *	@param enum     $hydration  One of Doctrine::HYDRATE_* hydration methods.
	 *	
	 *	@return Mixed
	 */
	 public function executeCache($params = array(), $hydration = Doctrine::HYDRATE_ARRAY)
	 {
	    if (!Zend_Registry::isRegistered('cachemanager')) {
	        throw new Kizano_Doctrine_Exception(__METHOD__ . '() depends on the ' .
	            'cache manager being registered in Zend_Registry as "cachemanager".');
	    }

	 	$cache = Zend_Registry::get('cachemanager')->getCache('generic');

	 	// We can't serialize PDO, so skip the caching on this query if it's for a record hydration.
	 	if (
	 		!$cache->getOption('caching')
	 		|| $hydration == Doctrine::HYDRATE_RECORD
	 		|| $hydration == Doctrine::HYDRATE_RECORD_HIERARCHY
 		) {
	 		return $this->execute($params, $hydration);
 		}
	 	$hash = $this->calculateQueryCacheHash($params);
	 	if ($cache->test($hash)) {
	 		return $cache->load($hash);
	 	} else {
	 		$result = $this->execute($params, $hydration);
	 		$cache->save($result, $hash);
	 		return $result;
	 	}
	 }

    /**
     *  Clears the cache for the query generated in this manner.
     *
     *  @param Array    $params   Parameters to pass into the query.
     *
     *  @return void
     */
    public function clearCache($params = array())
    {
        $cache = Zend_Registry::get('cachemanager')->getCache('generic');
        $hash = $this->calculateQueryCacheHash($params);
        if ($cache->test($hash)) {
            $cache->remove($hash);
        }
    }

	/**
	 *	Convenience method to execute the query and return the first item
	 *	of the collection.
	 *	
	 *	@param Array    $params     Params of the query.
	 *	@param enum     $hydration  Hydration method. Can be one of Doctrine::HYDRATE_*
	 *	
	 *	@return Mixed
	 */
	 public function fetchOneCache($params = array(), $hydration = Doctrine::HYDRATE_ARRAY)
	 {
		$collection = $this->executeCache($params, $hydrationMode);

		if (is_scalar($collection)) {
			return $collection;
		}

		if (count($collection) === 0) {
			return false;
		}

		if ($collection instanceof Doctrine_Collection) {
			return $collection->getFirst();
		} else if (is_array($collection)) {
			return array_shift($collection);
		}

		return false;
	 }

    /**
     *  Convenience method to fetch an array of cached items.
     *  
	 *	@param Array    $params     Params of the query.
	 *	@param enum     $hydration  Hydration method. Can be one of Doctrine::HYDRATE_*
	 *	
	 *	@return Mixed
     */
    public function fetchArrayCache($params = array(), $hydration = Doctrine::HYDRATE_ARRAY)
    {
        return $this->executeCache($params, $hydration);
    }

    /**
     * Create a new Cb_Query instance
     *
     * @param Doctrine_Connection   $conn
     *
     * @return Cb_Query
     */
    public /*overrides*/ static function create(Doctrine_Connection $conn = null, $class = null)
    {
        if ($conn !== null && !($conn instanceof Doctrine_Connection)) {
            throw new InvalidArgumentException(sprintf(
                "%s(): Argument 1 for this function expected Doctrine_Connection, recieved: `%s'",
                __METHOD__,
                getType($conn)
            ));
            return false;
        }

        return new $this($conn);
    }

	/**
	 * calculateQueryCacheHash
	 * calculate hash key for query cache
	 *
	 * @patch Changed the hash in this function to something more secure and less
	 *      likely to be repeated/broken.
	 *
	 * @return string	the hash
	 */
	public /*overrides*/ function calculateQueryCacheHash()
	{
		$dql = $this->getDql();
		// ripemd256 is ever so slightly faster than sha256
		$hash = hash('ripemd256', $dql . var_export($this->_pendingJoinConditions, true) . 'KIZANO_QUERY_CACHE_SALT');
		return $hash;
	}

	/**
	 * calculateResultCacheHash
	 * calculate hash key for result cache
	 *
	 * @patch Changed the hash in this function to something more secure and less
	 *      likely to be repeated/broken.
	 *
	 * @param array $params
	 * @return string	the hash
	 */
	public /*overrides*/ function calculateResultCacheHash($params = array())
	{
		$dql = $this->getDql();
		$conn = $this->getConnection();
		$params = $this->getFlattenedParams($params);
		// ripemd256 is ever so slightly faster than sha256
		$hash = hash(
			'ripemd256',
			$this->_hydrator->getHydrationMode() .
			$conn->getName() .
			$conn->getOption('dsn') . $dql .
			var_export($this->_pendingJoinConditions, true) .
			var_export($params, true)
		);

		return $hash;
	}

    /**
     * getLimitSubquery
     * this is method is used by the record limit algorithm
     *
     * when fetching one-to-many, many-to-many associated data with LIMIT clause
     * an additional subquery is needed for limiting the number of returned records instead
     * of limiting the number of sql result set rows
     *
     * @patch This function doesn't readily support tabels with multiple primary keys.
     *
     * @return string       the limit subquery
     * @todo A little refactor to make the method easier to understand & maybe shorter?
     */
    public /*overrides*/ function getLimitSubquery()
    {
        $map = reset($this->_queryComponents);
        $table = $map['table'];
        $componentAlias = key($this->_queryComponents);

        // get short alias
        $alias = $this->getSqlTableAlias($componentAlias);

        $driverName = $this->_conn->getAttribute(Doctrine_Core::ATTR_DRIVER_NAME);

        // initialize the base of the subquery
        if (($driverName == 'oracle' || $driverName == 'oci') && $this->_isOrderedByJoinedColumn()) {
            $subquery = 'SELECT ';
        } else {
            $subquery = 'SELECT DISTINCT ';
        }
        // what about composite keys?
        if (is_array($table->getIdentifier())) {
            foreach ($table->getIdentifier() as $id) {
                $primaryKey[] = $this->_conn->quoteIdentifier($alias . '.' . $id);
            }
            $subquery .= join(', ', $primaryKey);
        } else {
            $primaryKey = $alias . '.' . $table->getColumnName($table->getIdentifier());
            $subquery .= $this->_conn->quoteIdentifier($primaryKey);
        }

        // pgsql & oracle need the order by fields to be preserved in select clause
        if ($driverName == 'pgsql' || $driverName == 'oracle' || $driverName == 'oci' || $driverName == 'mssql' || $driverName == 'odbc') {
            foreach ($this->_sqlParts['orderby'] as $part) {
                // Remove identifier quoting if it exists
                $e = $this->_tokenizer->bracketExplode($part, ' ');
                foreach ($e as $f) {
                    if ($f == 0 || $f % 2 == 0) {
                        $partOriginal = str_replace(',', '', trim($f));
                        $callback = create_function('$e', 'return trim($e, \'[]`"\');');
                        $part = trim(implode('.', array_map($callback, explode('.', $partOriginal))));

                        if (strpos($part, '.') === false) {
                            continue;
                        }

                        // don't add functions
                        if (strpos($part, '(') !== false) {
                            continue;
                        }

                        // don't add primarykey column (its already in the select clause)
                        if ($part !== $primaryKey) {
                            $subquery .= ', ' . $partOriginal;
                        }
                    }
                }
            }
        }

        $orderby = $this->_sqlParts['orderby'];
        $having = $this->_sqlParts['having'];
        if ($driverName == 'mysql' || $driverName == 'pgsql') {
            foreach ($this->_expressionMap as $dqlAlias => $expr) {
                if (isset($expr[1])) {
                    $subquery .= ', ' . $expr[0] . ' AS ' . $this->_aggregateAliasMap[$dqlAlias];
                }
            }
        } else {
            foreach ($this->_expressionMap as $dqlAlias => $expr) {
                if (isset($expr[1])) {
                    foreach ($having as $k => $v) {
                        $having[$k] = str_replace($this->_aggregateAliasMap[$dqlAlias], $expr[0], $v);
                    }
                    foreach ($orderby as $k => $v) {
                        $e = explode(' ', $v);
                        if ($e[0] == $this->_aggregateAliasMap[$dqlAlias]) {
                            $orderby[$k] = $expr[0];
                        }
                    }
                }
            }
        }

        // Add having fields that got stripped out of select
        preg_match_all('/`[a-z0-9_]+`\.`[a-z0-9_]+`/i', implode(' ', $having), $matches, PREG_PATTERN_ORDER);
        if (count($matches[0]) > 0) {
            $subquery .= ', ' . implode(', ', array_unique($matches[0]));
        }

        $subquery .= ' FROM';

        foreach ($this->_sqlParts['from'] as $part) {
            // preserve LEFT JOINs only if needed
            if (substr($part, 0, 9) === 'LEFT JOIN') {
                $e = explode(' ', $part);
                // Fix for http://www.doctrine-project.org/jira/browse/DC-706
                // Fix for http://www.doctrine-project.org/jira/browse/DC-594
                if (empty($this->_sqlParts['orderby']) && empty($this->_sqlParts['where']) && empty($this->_sqlParts['having']) && empty($this->_sqlParts['groupby'])) {
                    continue;
                }
            }

            $subquery .= ' ' . $part;
        }

        // all conditions must be preserved in subquery
        $subquery .= ( ! empty($this->_sqlParts['where']))?   ' WHERE '    . implode(' ', $this->_sqlParts['where'])  : '';
        $subquery .= ( ! empty($this->_sqlParts['groupby']))? ' GROUP BY ' . implode(', ', $this->_sqlParts['groupby'])   : '';
        $subquery .= ( ! empty($having))?  ' HAVING '   . implode(' AND ', $having) : '';
        $subquery .= ( ! empty($orderby))? ' ORDER BY ' . implode(', ', $orderby)  : '';

        if (($driverName == 'oracle' || $driverName == 'oci') && $this->_isOrderedByJoinedColumn()) {
            // When using "ORDER BY x.foo" where x.foo is a column of a joined table,
            // we may get duplicate primary keys because all columns in ORDER BY must appear
            // in the SELECT list when using DISTINCT. Hence we need to filter out the
            // primary keys with an additional DISTINCT subquery.
            // #1038
            $quotedIdentifierColumnName = $this->_conn->quoteIdentifier($table->getColumnName($table->getIdentifier()));
            $subquery = 'SELECT doctrine_subquery_alias.' . $quotedIdentifierColumnName
                    . ' FROM (' . $subquery . ') doctrine_subquery_alias'
                    . ' GROUP BY doctrine_subquery_alias.' . $quotedIdentifierColumnName
                    . ' ORDER BY MIN(ROWNUM)';
        }

        // add driver specific limit clause
        $subquery = $this->_conn->modifyLimitSubquery($table, $subquery, $this->_sqlParts['limit'], $this->_sqlParts['offset']);

        $parts = $this->_tokenizer->quoteExplode($subquery, ' ', "'", "'");

        foreach ($parts as $k => $part) {
            if (strpos($part, ' ') !== false) {
                continue;
            }

            $part = str_replace(array('"', "'", '`'), "", $part);

            if ($this->hasSqlTableAlias($part)) {
                $parts[$k] = $this->_conn->quoteIdentifier($this->generateNewSqlTableAlias($part));
                continue;
            }

            if (strpos($part, '.') === false) {
                continue;
            }

            preg_match_all("/[a-zA-Z0-9_]+\.[a-z0-9_]+/i", $part, $m);

            foreach ($m[0] as $match) {
                $e = explode('.', $match);

                // Rebuild the original part without the newly generate alias and with quoting reapplied
                $e2 = array();
                foreach ($e as $k2 => $v2) {
                  $e2[$k2] = $this->_conn->quoteIdentifier($v2);
                }
                $match = implode('.', $e2);

                // Generate new table alias
                $e[0] = $this->generateNewSqlTableAlias($e[0]);

                // Requote the part with the newly generated alias
                foreach ($e as $k2 => $v2) {
                  $e[$k2] = $this->_conn->quoteIdentifier($v2);
                }

                $replace = implode('.' , $e);

                // Replace the original part with the new part with new sql table alias
                $parts[$k] = str_replace($match, $replace, $parts[$k]);
            }
        }

        if ($driverName == 'mysql' || $driverName == 'pgsql') {
            foreach ($parts as $k => $part) {
                if (strpos($part, "'") !== false) {
                    continue;
                }
                if (strpos($part, '__') == false) {
                    continue;
                }

                preg_match_all("/[a-zA-Z0-9_]+\_\_[a-z0-9_]+/i", $part, $m);

                foreach ($m[0] as $match) {
                    $e = explode('__', $match);
                    $e[0] = $this->generateNewSqlTableAlias($e[0]);

                    $parts[$k] = str_replace($match, implode('__', $e), $parts[$k]);
                }
            }
        }

        $subquery = implode(' ', $parts);
        return $subquery;
    }
}

