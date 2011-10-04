<?php
class Vps_Test_SeparateDb
{
    private static $_dbName = null;

    public static function getDbName()
    {
        return Vps_Test_SeparateDb::$_dbName;
    }

    public static function setDbAndCreateCookie($dbName)
    {
        setcookie('test_special_db', $dbName, time()+(10*60), '/');
        Vps_Registry::set('db', Vps_Test::getTestDb($dbName));
        Vps_Model_Abstract::clearInstances();
    }

    public static function setDbFromCookie()
    {
        if (!empty($_COOKIE['test_special_db'])) {
            Vps_Registry::set('db', Vps_Test::getTestDb($_COOKIE['test_special_db']));
        }
    }

    /**
     * Muss im Test separat aufgerufen werden wenn für den Test eine eigene DB
     * benötigt wird
     */
    public static function createSeparateTestDb($bootstrapFile)
    {
        if (substr($bootstrapFile, 0, 1) != '/') {
            throw new Vps_Exception("First argument 'bootstrapFile' must be an absolute path to the file");
        }

        $section = preg_replace('/[^a-zA-Z0-9]/', '', Vps_Registry::get('config')->getSectionName());
        Vps_Test_SeparateDb::$_dbName = 'test_'.$section.date('_Ymd_His_').substr(uniqid(), -5);
        $testDb = Vps_Test::getTestDb();
        $testDb->query('CREATE DATABASE '.Vps_Test_SeparateDb::getDbName());

        Vps_Registry::set('db', Vps_Test::getTestDb(Vps_Test_SeparateDb::getDbName()));

        $ret = null;
        passthru('mysql '.Vps_Test_SeparateDb::getDbName().' < '.$bootstrapFile, $ret);
        if ($ret != 0) throw new Vps_Exception("bootstrap file could not be processed through mysql");

        Vps_Model_Abstract::clearInstances();
    }

    public static function restoreTestDb()
    {
        $dbName = Vps_Test_SeparateDb::getDbName();
        if ($dbName) {
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
            $testDb->query('DROP DATABASE '.$dbName);
            Vps_Test_SeparateDb::$_dbName = null;
            Vps_Registry::set('db', $testDb);
        }
    }
}
