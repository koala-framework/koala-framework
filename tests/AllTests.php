<?php
if (!defined('PHPUnit_MAIN_METHOD')) {
    define('PHPUnit_MAIN_METHOD', 'AllTests::main');
}
require_once 'bootstrap.php';

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
                    if (substr($entry, -4) != '.php') continue;
                    $className = str_replace('.php', '', str_replace('/', '_', $entry));
                    if (is_instance_of($className, 'PHPUnit_Framework_TestCase')) {
                        $listarray[] = $entry;
                    }
                }
            }
        }
        return($listarray);
    }
}

if (PHPUnit_MAIN_METHOD == 'AllTests::main') {
    AllTests::main();
}
