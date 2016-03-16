<?php
class Kwf_Assets_Provider_CssByJs extends Kwf_Assets_Provider_Abstract
{
    private $_paths;
    public function __construct(array $paths)
    {
        $this->_paths = $paths;
    }

    public function getDependenciesForDependency(Kwf_Assets_Dependency_Abstract $dependency)
    {
        $ret = array();

        if ($dependency->getMimeType() == 'text/javascript' && $dependency instanceof Kwf_Assets_Dependency_File) {
            $fn = $dependency->getFileNameWithType();
            $match = false;
            foreach ($this->_paths as $p) {
                if ($p == substr($fn, 0, strlen($p))) {
                    $match = true;
                }
            }
            if ($match && substr($fn, -3) == '.js') {
                if (file_exists(substr($dependency->getAbsoluteFileName(), 0, -3).'.css')) {
                    $fnCss = substr($fn, 0, -3).'.css';
                    $ret[Kwf_Assets_Dependency_Abstract::DEPENDENCY_TYPE_REQUIRES][] = new Kwf_Assets_Dependency_File_Css($this->_providerList, $fnCss);
                }
                if (file_exists(substr($dependency->getAbsoluteFileName(), 0, -3).'.scss')) {
                    $fnScss = substr($fn, 0, -3).'.scss';
                    $ret[Kwf_Assets_Dependency_Abstract::DEPENDENCY_TYPE_REQUIRES][] = new Kwf_Assets_Dependency_File_Scss($this->_providerList, $fnScss);
                }
            }
        }
        return $ret;
    }

    public function getDependency($dependencyName)
    {
        return null;
    }
}
