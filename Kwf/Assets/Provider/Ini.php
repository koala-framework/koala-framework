<?php
class Kwf_Assets_Provider_Ini extends Kwf_Assets_Provider_Abstract
{
    private $_config;

    public function __construct($iniFile)
    {
        $this->_config = new Kwf_Config_Ini($iniFile, 'dependencies');
    }

    public function getDependency($dependencyName)
    {
        if (!$this->_config->$dependencyName) return null;
        $dep = $this->_config->$dependencyName;
        $ret = array();
        if (isset($dep->dep)) {
            foreach ($dep->dep as $i) {
                $ret[] = trim($i);
            }
        }
        if (isset($dep->files)) {
            foreach ($dep->files as $i) {
                $ret[] = Kwf_Assets_Dependency_File::createDependency(trim($i));
            }
        }
        return $ret;
    }
}
