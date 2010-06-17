<?php
class Vps_Test_OwnDbTestCase extends PHPUnit_Framework_TestCase
{
    private $_dbName = null;

    /**
     * Muss im Test separat aufgerufen werden wenn für den Test eine eigene DB
     * benötigt wird
     */
    protected function _createSeparateTestDb($bootstrapFile)
    {
        if (substr($bootstrapFile, 0, 1) != '/') {
            throw new Vps_Exception("First argument 'bootstrapFile' must be an absolute path to the file");
        }

        $section = preg_replace('/[^a-zA-Z0-9]/', '', Vps_Registry::get('config')->getSectionName());
        $this->_dbName = 'test_'.$section.date('_Ymd_His_').substr(uniqid(), -5);
        $testDb = Vps_Test::getTestDb();
        $testDb->query('CREATE DATABASE '.$this->_dbName);

        Vps_Registry::set('db', Vps_Test::getTestDb($this->_dbName));

        $ret = null;
        passthru('mysql '.$this->_dbName.' < '.$bootstrapFile, $ret);
        if ($ret != 0) throw new Vps_Exception("bootstrap file could not be processed through mysql");
    }

    public function setUp()
    {
        Vps_Model_Abstract::clearInstances();
    }

    public function tearDown()
    {
        if ($this->_dbName) {
            $testDb = Vps_Test::getTestDb();
            // alte test-dbs löschen
            $testDatabases = $testDb->query('SHOW DATABASES')->fetchAll();
            foreach ($testDatabases as $testDatabase) {
                if (preg_match('/^test_[^_]+_([0-9]+)_[0-9]+_[^_]+$/', $testDatabase['Database'], $matches)) {
                    // test-db löschen wenn sie älter als 2 tage is
                    if ((int)$matches[1] < (int)date('Ymd', time()-(2*86400))) {
                        $testDb->query('DROP DATABASE '.$testDatabase['Database']);
                    }
                }
            }
            $testDb->query('DROP DATABASE '.$this->_dbName);
            $this->_dbName = null;
            Vps_Registry::set('db', $testDb);
        }
    }
}
