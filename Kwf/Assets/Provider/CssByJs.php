<?php
class Kwf_Assets_Provider_CssByJs extends Kwf_Assets_Provider_Abstract
{
    private $_paths;
    private $_absolutePathsCache;
    public function __construct(array $paths)
    {
        $this->_paths = $paths;
    }

    private function _getAbsolutePaths()
    {
        if (!isset($this->_absolutePathsCache)) {
            $paths = Kwf_Config::getValueArray('path');
            $this->_absolutePathsCache = array();
            foreach ($this->_paths as $p) {
                $pathType = substr($p, 0, strpos($p, '/'));
                $f = substr($p, strpos($p, '/'));
                if (!isset($paths[$pathType])) {
                    throw new Kwf_Exception("Unknown path type: '$pathType'");
                }
                $this->_absolutePathsCache[] = $paths[$pathType].$f;

            }
        }
        return $this->_absolutePathsCache;
    }

    public function getDependenciesForDependency(Kwf_Assets_Dependency_Abstract $dependency)
    {
        $ret = array();

        if ($dependency->getMimeType() == 'text/javascript' && $dependency instanceof Kwf_Assets_Dependency_File) {
            $fn = $dependency->getFileName();
            if (substr($fn, -3) == '.js') {
                $matchFound = false;
                foreach ($this->_getAbsolutePaths() as $i) {
                    if (substr($fn, 0, strlen($i)) == $i) {
                        $matchFound = true;
                        break;
                    }
                }
                if ($matchFound) {
                    $fnCss = substr($fn, 0, -3).'.css';
                    if (file_exists($fnCss)) {
                        $ret[Kwf_Assets_Dependency_Abstract::DEPENDENCY_TYPE_REQUIRES][] = new Kwf_Assets_Dependency_File_Css($fnCss);
                    }
                    $fnScss = substr($fn, 0, -3).'.scss';
                    if (file_exists($fnScss)) {
                        $ret[Kwf_Assets_Dependency_Abstract::DEPENDENCY_TYPE_REQUIRES][] = new Kwf_Assets_Dependency_File_Scss($fnScss);
                    }
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
