<?php
/**
 *  Kizano_Test_PHPUnit_DatabaseTestCase
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
 *  @package    Test
 *  @copyright  Copyright (c) 2009-2011 Markizano Draconus <markizano@markizano.net>
 *  @license    http://framework.zend.com/license/new-bsd     New BSD License
 *  @author     Markizano Draconus <markizano@markizano.net>
 */

require_once 'PHPUnit/Extensions/Database/DataSet/DefaultDataSet.php';

/**
 * Database test case
 *
 * Encapsulate reusable code for DB testing, providing methods to compare
 * DB results with expected data stored in XML files.
 *
 * @notes This class was borrowed from @link and modified to suit our needs.
 * 
 * @package     Library
 * @author      Devis Lucato <devis@ibuildings.com>
 * @author      Markizano Draconus <markizano@markizano.net>
 * @copyright   Ibuildings UK
 * @date        November 2010
 * @link        https://github.com/ibuildings/zf-db-testing/blob/master/library/Ibuildings/Test/PHPUnit/DatabaseTestCase/Abstract.php
 */
class Kizano_Test_PHPUnit_DatabaseTestCase extends Zend_Test_PHPUnit_DatabaseTestCase
{

    const DEFAULT_CONNECTION_SCHEMA = 'ZF_test';

    /**
     * Variable to be defined in each test case
     *
     * @var string
     */
    protected $_initialSeedFile = '';

    /**
     * Allow to override the global configuration for a particular test case
     *
     * @var string
     */
    protected $_seedFilesPath = null;

    /**
     * Allow to override the global configuration for a particular test case
     *
     * @var string
     */
    protected $_expectedFilesPath = null;

    /**
     * Parameter for Zend_Test_PHPUnit_Db_Connection
     *
     * @var string
     */
    protected $_connectionSchema = self::DEFAULT_CONNECTION_SCHEMA;

    /**
     * Connection to testing database
     *
     * @var Zend_Test_PHPUnit_Db_Connection
     */
    protected $_connectionMock;

    /**
     * Application configuration
     *
     * @var Zend_Config_Ini
     */
    private $__configuration = null;

    /**
     *  Bootstraps the test case.
     *  
     *  @return void
     */
    public function setUp()
    {
        Zend_Registry::get('application')
            ->getBootstrap()
            ->bootstrap('autoload');
        parent::setUp();
    }

    /**
     * Returns the application configuration
     *
     * @return Zend_Config_Ini
     */
    public function getConfiguration()
    {
        if ($this->__configuration == null) {
            $this->__configuration = new Zend_Config(Zend_Registry::get('application')->getOptions());
        }

        return $this->__configuration;
    }

    /**
     * Returns the seed files folder path
     *
     * @return string
     */
    public function getSeedFilesPath()
    {
        if ($this->_seedFilesPath == null) {
            $seedFilesPath = $this->getConfiguration()->tests->seeds->folder;
            $this->_seedFilesPath = rtrim($seedFilesPath, '/') . '/';
        }

        return $this->_seedFilesPath;
    }

    /**
     * Returns the expected files folder path
     *
     * @return string
     */
    public function getExpectedFilesPath()
    {
        if ($this->_expectedFilesPath == null) {
            $filesPath = $this->getConfiguration()->tests->expected->folder;
            $this->_expectedFilesPath = rtrim($filesPath, '/') . '/';
        }

        return $this->_expectedFilesPath;
    }

    /**
     * Returns the test database connection.
     *
     * @return PHPUnit_Extensions_Database_DB_IDatabaseConnection
     */
    protected function getConnection()
    {
        if ($this->_connectionMock == null) {
            $dbAdapterName = $this->getConfiguration()->tests->db->adapter;
            $dbAdapterParams = $this->getConfiguration()->tests->db->params->toArray();

            $connection = Zend_Db::factory($dbAdapterName, $dbAdapterParams);

            $this->_connectionMock = $this->createZendDbConnection(
                $connection, $this->_connectionSchema
            );

            Zend_Db_Table_Abstract::setDefaultAdapter($connection);
        }

        return $this->_connectionMock;
    }

    /**
     * Retrieve from flat XML files data used to populate the database
     *
     * @return PHPUnit_Extensions_Database_DataSet_IDataSet
     */
    protected function getDataSet()
    {
        if (empty($this->_initialSeedFile)) {
            throw new Kizano_Test_Exception(sprintf(
                'You must define a value for %s::_initialSeedFile!',
                get_class($this)
            ));
        }

        return $this->createFlatXmlDataSet($this->getSeedFilesPath() . $this->_initialSeedFile);
    }

    /**
     * Convert a Rowset to a Dataset
     *
     * @param  Zend_Db_Table_Rowset_Abstract $rowset
     * @param  string $tableName
     * @return PHPUnit_Extensions_Database_DataSet_DefaultDataSet
     */
    public function convertRowsetToDataSet($rowset, $tableName = null)
    {
        $rowsetDataSet = new Zend_Test_PHPUnit_Db_DataSet_DbRowset($rowset, $tableName);
        return new PHPUnit_Extensions_Database_DataSet_DefaultDataSet(array($rowsetDataSet));
    }

    /**
     * Convert a Record to a Dataset
     *
     * @param  array $data
     * @param  string $tableName
     * @return PHPUnit_Extensions_Database_DataSet_DefaultDataSet
     */
    public function convertRecordToDataSet(Array $data, $tableName)
    {
        $rowset = new Zend_Db_Table_Rowset(array('data' => array($data)));
        return $this->convertRowsetToDataSet($rowset, $tableName);
    }

    /**
     * Compare dataset with data stored in the file
     *
     * @param  string $filename
     * @param  PHPUnit_Extensions_Database_DataSet_IDataSet $expected
     * @return boolean
     */
    public function assertDataSetsMatchXML($filename, PHPUnit_Extensions_Database_DataSet_IDataSet $actual)
    {
        if (empty($filename) || !is_string($filename)) {
            throw new InvalidArgumentException(
              'Second parameter "filename" is not a valid string.'
            );
        }

        $expected = $this->createFlatXmlDataSet($this->getExpectedFilesPath() . $filename);

        return $this->assertDataSetsEqual($expected, $actual);
    }
}

