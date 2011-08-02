<?php
class Vps_Test_TestSuite extends PHPUnit_Framework_TestSuite
{
    public function __construct($name = 'Vps Framework')
    {
        parent::__construct($name);
        $this->setBackupGlobals(false);
        $classes = $this->_addDirectory('./tests', false);

        // damit der js test auch im web immer ausgeführt wird
        if (!in_array('Vps_Js_SyntaxTest', $classes)) {
            $classes[] = 'Vps_Js_SyntaxTest';
        }

        if (file_exists("/www/testtimes")) {
            $app = Vps_Registry::get('config')->application->id;
            if (!file_exists("/www/testtimes/failure_$app")) mkdir("/www/testtimes/failure_$app");

            $times = array();
            foreach ($classes as $k=>$c) {
                $times[$k] = 0;
                $p = "/www/testtimes/failure_$app/".$c;
                if (file_exists($p)) {
                    $times[$k] = file_get_contents($p);
                }
            }
            arsort($times);
            $sortedClasses = array();
            foreach (array_keys($times) as $k) {
                $sortedClasses[] = $classes[$k];
            }
            $classes = $sortedClasses;
        }
        foreach ($classes as $c) {
            $this->addTestSuite($c);
        }

    }

    private function _addDirectory($basePath, $onlyTestPrefix)
    {
        if (!file_exists($basePath)) return array();

        $dir = new Vps_Iterator_Filter_Php(
            new RecursiveIteratorIterator(new RecursiveDirectoryIterator($basePath), true)
        );

        $ret = array();
        foreach ($dir as $file) {
            $file = substr($file, strlen($basePath)+1);
            if (substr($file, -8) != 'Test.php' && $onlyTestPrefix) {
                continue;
            }
            $className = str_replace(array('./', '.php', '/'), array('', '', '_'), $file);
            if (class_exists($className) && is_instance_of($className, 'PHPUnit_Framework_TestCase')) {
                $ret[] = $className;
            }
        }
        return $ret;
    }

    public function getFilteredTests($filter = FALSE, array $groups = array(), array $excludeGroups = array())
    {
        return self::_getFilteredTests($this, $filter, $groups, $excludeGroups);
    }


    //code kopiert von PHPUnit_Framework_TestSuite::run
    //nicht ganz perfekt weil theoretisch eine TestSuite run überschreiben könnte
    //aber für uns reichts
    private static function _getFilteredTests($testSuite, $filter, array $groups, array $excludeGroups)
    {
        $ret = array();

        if (empty($groups)) {
            $tests = $testSuite->tests;
        } else {
            $tests = array();

            foreach ($groups as $group) {
                if (isset($testSuite->groups[$group])) {
                    $tests = array_merge($tests, $testSuite->groups[$group]);
                }
            }
        }

        foreach ($tests as $test) {
            if ($test instanceof PHPUnit_Framework_TestSuite) {
                $ret = array_merge($ret, self::_getFilteredTests($test, $filter, $groups, $excludeGroups));
            } else {
                $runTest = TRUE;

                if ($filter !== FALSE ) {
                    $tmp = PHPUnit_Util_Test::describe($test, FALSE);

                    if ($tmp[0] != '') {
                        $name = join('::', $tmp);
                    } else {
                        $name = $tmp[1];
                    }

                    if (preg_match($filter, $name) == 0) {
                        $runTest = FALSE;
                    }
                }

                if ($runTest && !empty($excludeGroups)) {
                    foreach ($testSuite->groups as $_group => $_tests) {
                        if (in_array($_group, $excludeGroups)) {
                            foreach ($_tests as $_test) {
                                if ($test === $_test) {
                                    $runTest = FALSE;
                                    break 2;
                                }
                            }
                        }
                    }
                }

                if ($runTest) {
                    $ret[] = $test;
                }
            }
        }

        return $ret;
    }
}
