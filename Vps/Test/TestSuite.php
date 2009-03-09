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
}
