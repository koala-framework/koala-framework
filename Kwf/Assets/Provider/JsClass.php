<?php
class Kwf_Assets_Provider_JsClass extends Kwf_Assets_Provider_Abstract
{
    private $_basePath;
    private $_namespace;
    public function __construct($basePath, $namespace = null)
    {
        $this->_basePath = $basePath;
        $this->_namespace = $namespace;
    }

    public function getDependency($dependencyName)
    {
        if ($this->_namespace) {
            if (substr($dependencyName, 0, strlen($this->_namespace)+1) == $this->_namespace.'.') {
                $dependencyName = substr($dependencyName, strlen($this->_namespace)+1);
            }
        }
        $d = $this->_basePath.'/'.str_replace('.', '/', $dependencyName).'.js';
        if (file_exists($d)) {
            return Kwf_Assets_Dependency_File::createDependency($d, $this->_providerList);
        }
        return null;
    }
}
