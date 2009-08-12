<?php
class Vps_Test_TestSuite extends PHPUnit_Framework_TestSuite
{
    public function __construct($name = 'Vps Framework')
    {
        parent::__construct($name);
        $this->_addDirectory(VPS_PATH.'/tests', true);
        $this->_addDirectory('./tests', false);
    }

    private function _addDirectory($basePath, $onlyTestPrefix)
    {
        if (!file_exists($basePath)) return;

        set_include_path(
            get_include_path().PATH_SEPARATOR.$basePath
        );
        $dir = new Vps_Iterator_Filter_Php(
            new RecursiveIteratorIterator(new RecursiveDirectoryIterator($basePath), true)
        );

        foreach ($dir as $file) {
            $file = substr($file, strlen($basePath)+1);
            if (substr($file, -8) != 'Test.php' && $onlyTestPrefix) {
                continue;
            }
            $className = str_replace(array('./', '.php', '/'), array('', '', '_'), $file);
            if (class_exists($className) && is_instance_of($className, 'PHPUnit_Framework_TestCase')) {
                require_once($file);
                $this->addTestSuite($className);
            }
        }
    }

    public function getFilteredTests($filter = FALSE, array $groups = array(), array $excludeGroups = array())
    {
        return self::_getFilteredTests($this, $filter, $groups, $excludeGroups);
    }

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
