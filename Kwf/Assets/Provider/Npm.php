<?php
class Kwf_Assets_Provider_Npm extends Kwf_Assets_Provider_Abstract
{
    private $_path;
    public function __construct($path)
    {
        $path = substr($path, strlen('node_modules/'));
        $this->_path = $path;
    }

    private function _guessMainFiles($dependencyName)
    {
        $dir = 'node_modules/'.$this->_path;
        $path = $this->_path;

        $ret = null;
        if (file_exists($dir . '/index.js')) {
            $ret = $path . '/index.js';
        } else if (file_exists($dir . "/$path.js")) {
            $ret = $path . "/$path.js";
        } else {
            throw new Kwf_Exception("Can't find dependency for $dependencyName in node_modules/$this->_path");
        }
        return $ret;
    }

    public function getDependencyNameByAlias($aliasDependencyName)
    {
        $ret = null;
        if ($aliasDependencyName == $this->_path) {
            $type = $this->_path;
            if (substr($type, -3) == '.js') {
                $type = substr($type, 0, -3);
            }
            $dir = 'node_modules/'.$this->_path;
            if (file_exists($dir.'/package.json')) {
                $package = json_decode(file_get_contents($dir.'/package.json'), true);
                if (isset($package['main'])) {
                    if (file_exists($dir . "/" . $package['main'])) {
                        $ret = $type.'/'.$package['main'];
                    } else {
                        $ret = $type.'/'.$package['main'] . '.js';
                    }

                } else {
                    $ret = $this->_guessMainFiles($aliasDependencyName);
                }
            } else {
                $ret = $this->_guessMainFiles($aliasDependencyName);
            }
        } else if  (substr(strtolower($aliasDependencyName), 0, strlen($this->_path)+1) == strtolower($this->_path).'/') {
            //absolute path to single file path given
            if (is_file("node_modules/$aliasDependencyName")) {
                $ret = $aliasDependencyName;
            } else if (file_exists("node_modules/$aliasDependencyName.js")) {
                $ret = $aliasDependencyName . '.js';
            } else if (is_dir("node_modules/$aliasDependencyName/") && file_exists("node_modules/$aliasDependencyName/index.js")) {
                $ret = $aliasDependencyName . '/index.js';
            }
        }
        return $ret;
    }

    public function getDependency($dependencyName)
    {
        $ret = null;
        if (substr(strtolower($dependencyName), 0, strlen($this->_path)+1) == strtolower($this->_path).'/') {
            $ret = new Kwf_Assets_Dependency_File_Js($this->_providerList, $dependencyName);
        }
        return $ret;
    }

    public function getPathTypes()
    {
        $ret = array();
        foreach (glob('node_modules/*') as $i) {
            $type = substr($i, strlen('node_modules/'));
            $ret[$type] = $i;
        }
        return $ret;
    }
}

