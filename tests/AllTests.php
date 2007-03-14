<?php
error_reporting(E_ALL|E_STRICT);
if (!defined('PHPUnit_MAIN_METHOD')) {
    define('PHPUnit_MAIN_METHOD', 'AllTests::main');
}

function p($src, $max_depth = 3) {
    Zend::dump($src);
    /*
    ini_set('xdebug.var_display_max_depth', $max_depth);
    if(function_exists('xdebug_var_dump')) {
        xdebug_var_dump($src);
    } else {
        echo "<pre>";
        print_r($src);
        echo "</pre>";
    }
    */
}

require_once 'TestConfiguration.php';

require_once 'PHPUnit/Framework/TestSuite.php';
require_once 'PHPUnit/TextUI/TestRunner.php';

/**
 * Prepend library/ to the include_path.  This allows the tests to run out of the box and
 * helps prevent finding other copies of the framework that might be present.
 */
set_include_path(dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . 'library'
                 . PATH_SEPARATOR . '.' . PATH_SEPARATOR . get_include_path());

require_once 'Zend.php';
function __autoload($class)
{
    Zend::loadClass($class);
}

class E3_Test extends PHPUnit_Framework_TestCase
{
	protected function createDao()
	{
        $dbConfig = new Zend_Config_Ini('../application/config.db.ini', 'web');
        $dbConfig = $dbConfig->database->asArray();
        $db = Zend_Db::factory('PDO_MYSQL', $dbConfig);
        return new E3_Dao($db);
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
        $suite = new PHPUnit_Framework_TestSuite('E3 Framework');
		foreach (self::dirlist('E3') as $filename) {
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
