<?php
class Kwf_Assets_Provider_JsClass extends Kwf_Assets_Provider_Abstract
{
    private $_basePath;
    public function __construct($basePath)
    {
        $this->_basePath = $basePath;
    }

    public function getDependency($dependencyName)
    {
        $d = $this->_basePath.'/'.str_replace('.', '/', $dependencyName).'.js';
        if (file_exists($d)) {
            return Kwf_Assets_Dependency_File::createDependency($d, $this->_providerList);
        }
        return null;
    }
}
