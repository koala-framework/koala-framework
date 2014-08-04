<?php
class Kwf_Assets_Provider_BowerBuiltFile extends Kwf_Assets_Provider_Abstract
{
    public function __construct($path)
    {
        $this->_path = substr($path, strlen('bower_components/'));
    }

    public function getDependency($dependencyName)
    {
        if (strtolower($dependencyName) == $this->_path) {
            $dir = 'bower_components/'.$this->_path;
            $files = array(
                $this->_path.'.js',
                'dist/'.$this->_path.'.js',
                'src/'.$this->_path.'.js',
                'js/'.$this->_path.'.js',
            );
            foreach ($files as $f) {
                if (file_exists($dir.'/'.$f)) {
                    return new Kwf_Assets_Dependency_File_Js($this->_path.'/'.$f);
                }
            }
            throw new Kwf_Exception("Can't find built dependency for $dependencyName in bower_components/$this->_path");
        }
        return null;
    }
}
