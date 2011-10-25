<?php
class Kwf_Test_SeparateDb
{
    private static $_dbName = null;

    public static function getDbName()
    {
        return Kwf_Test_SeparateDb::$_dbName;
    }

    public static function setDbAndCreateCookie($dbName)
    {
        setcookie('test_special_db', $dbName, time()+(10*60), '/');
        Kwf_Registry::set('db', Kwf_Test::getTestDb($dbName));
        Kwf_Model_Abstract::clearInstances();
    }

    public static function setDbFromCookie()
    {
        if (!empty($_GET['test_special_db'])) {
            self::setDbAndCreateCookie($_GET['test_special_db']);
        }
        if (!empty($_COOKIE['test_special_db'])) {
            Kwf_Registry::set('db', Kwf_Test::getTestDb($_COOKIE['test_special_db']));
        }
    }

    /**
     * Muss im Test separat aufgerufen werden wenn für den Test eine eigene DB
     * benötigt wird
     */
    public static function createSeparateTestDb($bootstrapFile)
    {
        if (substr($bootstrapFile, 0, 1) != '/') {
            throw new Kwf_Exception("First argument 'bootstrapFile' must be an absolute path to the file");
        }

        $section = preg_replace('/[^a-zA-Z0-9]/', '', Kwf_Registry::get('config')->getSectionName());
        Kwf_Test_SeparateDb::$_dbName = 'test_'.$section.date('_Ymd_His_').substr(uniqid(), -5);
        $testDb = Kwf_Test::getTestDb();
        $testDb->query('CREATE DATABASE '.Kwf_Test_SeparateDb::getDbName());

        Kwf_Registry::set('db', Kwf_Test::getTestDb(Kwf_Test_SeparateDb::getDbName()));

        $ret = null;
        passthru('mysql '.Kwf_Test_SeparateDb::getDbName().' < '.$bootstrapFile, $ret);
        if ($ret != 0) throw new Kwf_Exception("bootstrap file could not be processed through mysql");

        Kwf_Model_Abstract::clearInstances();
    }

    public static function restoreTestDb()
    {
        $dbName = Kwf_Test_SeparateDb::getDbName();
        if ($dbName) {
            $testDb = Kwf_Test::getTestDb();
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
            $testDb->query('DROP DATABASE '.$dbName);
            Kwf_Test_SeparateDb::$_dbName = null;
            Kwf_Registry::set('db', $testDb);
        }
    }
}
