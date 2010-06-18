<?php
class Vps_Test
{
    public static function getTestDb($dbName = 'test')
    {
        $db = Zend_Db::factory('PDO_MYSQL', array(
            'host'=>'localhost',
            'username'=>'test',
            'password'=>'test',
            'dbname'=>$dbName
        ));
        $db->query('SET names UTF8');
        if (Zend_Registry::get('config')->debug->querylog) {
            $profiler = new Vps_Db_Profiler(true);
            $db->setProfiler($profiler);
        } else if (Zend_Registry::get('config')->debug->benchmark || Zend_Registry::get('config')->debug->benchmarkLog) {
            $profiler = new Vps_Db_Profiler_Count(true);
            $db->setProfiler($profiler);
        }

        return $db;
    }

    public static function setup()
    {
        $include_path  = get_include_path();
        $include_path .= PATH_SEPARATOR . VPS_PATH;
        set_include_path($include_path);

        require_once 'Vps/Loader.php';
        require_once 'Vps/Setup.php';
        Vps_Loader::registerAutoload();

        date_default_timezone_set('Europe/Berlin');
        mb_internal_encoding('UTF-8');

        Zend_Registry::setClassName('Vps_Registry');

        // auskommentiert, da main() sowieso nicht aufgerufen wird
//         require_once VPS_PATH.'/tests/TestConfiguration.php';

        require_once 'PHPUnit/Framework/TestSuite.php';
        require_once 'PHPUnit/TextUI/TestRunner.php';
    }

    // wird und wurde nie aufgerufen.
    public static function main()
    {
        $parameters = array();

        if (TESTS_GENERATE_REPORT && extension_loaded('xdebug')) {
            $parameters['reportDirectory'] = TESTS_GENERATE_REPORT_TARGET;
        }

        PHPUnit_TextUI_TestRunner::run(self::suite(), $parameters);
    }

    public static function suite()
    {
        $suite = new PHPUnit_Framework_TestSuite('Vps Framework');

        $dir = new Vps_Iterator_Filter_Php(
            new RecursiveIteratorIterator(new RecursiveDirectoryIterator('.'), true)
        );

        foreach ($dir as $file) {
            $className = str_replace(array('./', '.php', '/'), array('', '', '_'), $file);
            if (class_exists($className) && is_instance_of($className, 'PHPUnit_Framework_TestCase')) {
                require_once($file);
                $suite->addTestSuite($className);
            }
        }

        return $suite;
    }
}
