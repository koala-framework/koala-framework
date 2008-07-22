<?php
error_reporting(E_ALL|E_STRICT);
if (!defined('PHPUnit_MAIN_METHOD')) {
    define('PHPUnit_MAIN_METHOD', 'AllTests::main');
}
define('VPS_PATH', dirname(__FILE__).DIRECTORY_SEPARATOR.'..');
$include_path  = get_include_path();
$include_path .= PATH_SEPARATOR . VPS_PATH;
set_include_path($include_path);
require_once 'Vps/Loader.php';
Vps_Loader::registerAutoload();


require_once 'TestConfiguration.php';

require_once 'PHPUnit/Framework/TestSuite.php';
require_once 'PHPUnit/TextUI/TestRunner.php';
require_once 'PHPUnit/Extensions/ExceptionTestCase.php';

class E3_Test extends PHPUnit_Framework_TestCase
{
    protected function createDao()
    {
        $dbConfig = new Zend_Config_Ini('../application/config.db.ini', 'database');
        return new E3_Dao($dbConfig);
    }
}

class E3_ExceptionTest extends PHPUnit_Extensions_ExceptionTestCase
{
    protected function createDao()
    {
        $dbConfig = new Zend_Config_Ini('../application/config.db.ini', 'database');
        return new E3_Dao($dbConfig);
    }
}

class AllTests
{
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
        foreach (self::dirlist('Vpc') as $filename) {
            require_once($filename);
            $suite->addTestSuite(str_replace('.php', '', str_replace('/', '_', $filename)));
        }
        foreach (self::dirlist('Vps') as $filename) {
            require_once($filename);
            $suite->addTestSuite(str_replace('.php', '', str_replace('/', '_', $filename)));
        }

        return $suite;
    }

    public static function dirlist($dir) {
        $listarray = array();
        foreach (scandir($dir) as $entry) {
            if ($entry != '.' && $entry != '..' && $entry != '.svn') {
                $entry  = $dir.'/'.$entry;
                if (is_dir($entry)) {
                    $listarray = array_merge($listarray, self::dirlist($entry));
                } else {
                    $listarray[] = $entry;
                }
            }
        }
        return($listarray);
    }
}

if (PHPUnit_MAIN_METHOD == 'AllTests::main') {
    AllTests::main();
}
