<?php
if (!defined('PHPUnit_MAIN_METHOD')) {
    define('PHPUnit_MAIN_METHOD', 'E3_AllTests::main');
}

require_once 'PHPUnit/Framework/TestSuite.php';
require_once 'PHPUnit/TextUI/TestRunner.php';

require_once 'E3/WebTest.php';


class E3_AllTests
{
    public static function main()
    {
        PHPUnit_TextUI_TestRunner::run(self::suite());
    }

    public static function suite()
    {
        $suite = new PHPUnit_Framework_TestSuite('E3 Framework - E3');

        $suite->addTestSuite('E3_WebTest');
        return $suite;
    }
}

if (PHPUnit_MAIN_METHOD == 'E3_AllTests::main') {
    E3_AllTests::main();
}
