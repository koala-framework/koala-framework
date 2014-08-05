<?php
class Kwf_Assets_Provider_BowerBuiltFile extends Kwf_Assets_Provider_Abstract
{
    private $_path;
    public function __construct($path)
    {
        $path = substr($path, strlen('vendor/bower_components/'));
        $this->_path = $path;
    }

    public function getDependency($dependencyName)
    {
        if (strtolower($dependencyName) == $this->_path || strtolower($dependencyName).'.js' == $this->_path) {
            $dir = 'vendor/bower_components/'.$this->_path;
            $files = array(
                $this->_path.'.js',
                'dist/'.$this->_path.'.js',
                'src/'.$this->_path.'.js',
                'js/'.$this->_path.'.js',
            );
            foreach ($files as $f) {
                if (substr($f, -6) == '.js.js') $f = substr($f, 0, -3);
                if (file_exists($dir.'/'.$f)) {
                    return new Kwf_Assets_Dependency_File_Js($this->_path.'/'.$f);
                }
            }
            throw new Kwf_Exception("Can't find built dependency for $dependencyName in vendor/bower_components/$this->_path");
        }
        return null;
    }
}
