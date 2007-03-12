<?php
if (!defined('PHPUnit_MAIN_METHOD')) {
    define('PHPUnit_MAIN_METHOD', 'AllTests::main');
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

require_once 'E3/AllTests.php';

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

        $suite->addTest(E3_AllTests::suite());

        return $suite;
    }
}

if (PHPUnit_MAIN_METHOD == 'AllTests::main') {
    AllTests::main();
}
