<?php
require_once 'conf_global.php';

class MyApp_DbUnit_ArrayDataSet extends PHPUnit_Extensions_Database_DataSet_AbstractDataSet
{
    /**
     * @var array
     */
    protected $tables = array();

    /**
     * @param array $data
     */
    public function __construct(array $data)
    {
        foreach ($data AS $tableName => $rows) { 
            $columns = array();
            if (isset($rows[0])) {
                $columns = array_keys($rows[0]);
            }

            $metaData = new PHPUnit_Extensions_Database_DataSet_DefaultTableMetaData($tableName, $columns);
            $table = new PHPUnit_Extensions_Database_DataSet_DefaultTable($metaData);

            foreach ($rows AS $row) {
                $table->addRow($row); 
            }
            $this->tables[$tableName] = $table; 
        }
    }

    protected function createIterator($reverse = FALSE)
    {
        return new PHPUnit_Extensions_Database_DataSet_DefaultTableIterator($this->tables, $reverse); 
    }

    public function getTable($tableName)
    {
        if (!isset($this->tables[$tableName])) {
            throw new InvalidArgumentException("$tableName is not a table in the current database."); 
        }

        return $this->tables[$tableName]; 
    }
}

class MySqlTest extends PHPUnit_Extensions_Database_TestCase
{
    protected static $__is_init;
    protected static $__conn;

    protected $__debug = false;

    protected function setUp()
    {
        parent::setUp();

        if ($this->__debug) {
            echo __METHOD__ . "\n";
        }
    }

    protected function getDataSet($tbl = '')
    {
        if ($this->__debug) {
            echo __METHOD__ . "\n";
        }

        $rand = 0; //mt_rand(1, 99999);
        $ver = 0; //mt_rand(1, 100);

        $dataSets = array(
            'session' => array(
                array('sess_key' => 'a', 'sess_val' => 'Hello buddy!', 'expire_time' => $rand),
                array('sess_key' => 'b', 'sess_val' => 'I like it!', 'expire_time' => $rand),
            ),
                /*
                'acct_pkey' => array(
                    array('acct_id' => 1, 'key_id' => 1, 'version' => $ver, 'expire_time' => 0, 'is_abandon' => 0, 'abandon_time' => 0),
                    array('acct_id' => 2, 'key_id' => 2, 'version' => $ver, 'expire_time' => 0, 'is_abandon' => 0, 'abandon_time' => 0),
                ),
                 */
            'acct_pkey' => array(
                array('acct_id' => 1, 'key_id' => 1, 'version' => $ver),
                array('acct_id' => 2, 'key_id' => 2, 'version' => $ver),
            ),
        );

        $data = [];
        switch ($tbl) {
        case 'special':
            $data = [
                'session' => array(
                    array('sess_key' => 'a'),
                    array('sess_key' => 'b'),
                ),
            ];
            break;

        default:
            if (empty($tbl)) {
                $data = $dataSets;
            } else {
                $data = [ $tbl => $dataSets[$tbl] ];
            }
        }

        return new MyApp_DbUnit_ArrayDataSet($data);
    }

    protected function getConnection()
    {
        if ($this->__debug) {
            echo __METHOD__ . "\n";
        }

        if (!isset(self::$__is_init)) {
            $app  = new Yaf_Application(PATH_RHEA_ETC . "application.ini", 'rhea');
            $app->bootstrap();
            self::$__is_init = true;
        }

        if (!self::$__conn) {
            require_once PATH_RHEA_LIB . 'em_db.class.php';
            $db = em_db::singleton();
            $pdo = $db->get_connection();

            self::$__conn = $this->createDefaultDBConnection($pdo, em_config::get('db_name'));
        }

        return self::$__conn;
    }

    public function testDataNum()
    {
        if ($this->__debug) {
            echo __METHOD__ . "\n";
        }

        $this->assertEquals(2, self::$__conn->getRowCount('session'));
    }

    public function testTable()
    {
        if ($this->__debug) {
            echo __METHOD__ . "\n";
        }

        $queryTable = self::$__conn->createQueryTable('acct_pkey', 'SELECT acct_id, key_id, version FROM acct_pkey');
        $expected = $this->getDataSet()->getTable('acct_pkey');
        $this->assertTablesEqual($expected, $queryTable);
    }

    public function testDataSet()
    {
        if ($this->__debug) {
            echo __METHOD__ . "\n";
        }

        // Assert 1
        $dataSet = self::$__conn->createDataSet([ 'session' ]);
        $expected = $this->getDataSet('session');
        $this->assertDataSetsEqual($expected, $dataSet);

        // Assert 2
        $dataSet = new PHPUnit_Extensions_Database_DataSet_QueryDataSet(self::$__conn);
        $dataSet->addTable('session', 'SELECT sess_key FROM session'); 
        $expectedDataSet = $this->getDataSet('special');
        $this->assertDataSetsEqual($expectedDataSet, $dataSet);
    }
}

