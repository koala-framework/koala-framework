<?php
class Kwf_Assets_Provider_JsClass extends Kwf_Assets_Provider_Abstract
{
    private $_basePath;
    private $_basePathWithType;
    private $_namespace;
    public function __construct($basePath, $basePathWithType, $namespace = null)
    {
        $this->_basePath = $basePath;
        $this->_basePathWithType = $basePathWithType;
        $this->_namespace = $namespace;
    }

    public function getDependency($dependencyName)
    {
        if ($this->_namespace) {
            if (substr($dependencyName, 0, strlen($this->_namespace)+1) == $this->_namespace.'.') {
                $dependencyName = substr($dependencyName, strlen($this->_namespace)+1);
            } else {
                return null;
            }
        }
        $d = $this->_basePath.'/'.str_replace('.', '/', $dependencyName).'.js';
        if (file_exists($d)) {
            $d = $this->_basePathWithType.'/'.str_replace('.', '/', $dependencyName).'.js';
            return Kwf_Assets_Dependency_File::createDependency($d, $this->_providerList);
        }
        return null;
    }
}
