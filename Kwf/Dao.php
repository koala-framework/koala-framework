<?php
class Kwf_Dao
{
    private $_config;
    private $_tables = array();
    private $_db = array();
    private $_pageData = array();

    public function __construct(array $config = null)
    {
        if (is_null($config)) {
            $config = Kwf_Config::getValueArray('database');
        }
        $this->_config = $config;
    }

    /**
     * @deprecated
     */
    public static function getTable($tablename, $config = array())
    {
        static $tables;
        if (!isset($tables[$tablename])) {
            $tables[$tablename] = new $tablename($config);
        }
        return $tables[$tablename];
    }

    public function getDbConfig($db = 'web')
    {
        if (!isset($this->_config[$db])) {
            throw new Kwf_Dao_Exception("Connection \"$db\" in config not found.
                    Please add database.$db.host, database.$db.username, database.$db.password and database.$db.dbname to config.local.ini.");
        }
        $dbConfig = $this->_config[$db];
        if (!isset($dbConfig['username']) && isset($dbConfig['user'])) $dbConfig['username'] = $dbConfig['user'];
        if (!isset($dbConfig['password']) && isset($dbConfig['pass'])) $dbConfig['password'] = $dbConfig['pass'];
        if (!isset($dbConfig['dbname']) && isset($dbConfig['name'])) $dbConfig['dbname'] = $dbConfig['name'];
        return $dbConfig;
    }

    public function getDb($db = 'web')
    {
        if (!isset($this->_db[$db])) {
            $dbConfig = $this->getDbConfig($db);
            $this->_db[$db] = Zend_Db::factory('PDO_MYSQL', $dbConfig);
            $this->_db[$db]->query('SET names UTF8');

            /**
             * lc_time_names wird hier nicht gesetzt weil man für trlKwf
             * momentan das userModel benötigt und das gibts ohne DB
             * Verbindung nicht -> Endlosschleifen gefahr.
             * lc_time_names wurde früher vermutlich im TreeCache noch benötigt
             * (z.B. bei den News Month), aber das macht jetzt das PHP, dehalb
             * ist es nicht mehr nötig dies zu setzen.
             */
//             $this->_db[$db]->query("SET lc_time_names = '".trlKwf('en_US')."'");


            if (Kwf_Config::getValue('debug.querylog')) {
                $profiler = new Kwf_Db_Profiler(true);
                $this->_db[$db]->setProfiler($profiler);
            } else if (Kwf_Config::getValue('debug.queryTimeout')) {
                $profiler = new Kwf_Db_Profiler_Timeout(Kwf_Config::getValue('debug.queryTimeout'), true);
                $this->_db[$db]->setProfiler($profiler);
            } else if (Kwf_Benchmark::isEnabled() || Kwf_Benchmark::isLogEnabled()) {
                $profiler = new Kwf_Db_Profiler_Count(true);
                $this->_db[$db]->setProfiler($profiler);
            }
        }
        return $this->_db[$db];
    }

    public function hasDb($db = 'web')
    {
        return isset($this->_db[$db]);
    }

    public function getMongoDb()
    {
        static $ret;
        if (!isset($ret)) {
            $connection = new Mongo(); // connects to localhost:27017
            $mongoDb = Kwf_Config::getValue('server.mongo.database');
            $ret = $connection->$mongoDb;
        }
        return $ret;
    }
}
