<?php
class Kwf_Assets_Provider_AtRequires extends Kwf_Assets_Provider_Abstract
{
    public function getDependency($dependencyName)
    {
        return null;
    }

    public function getDependenciesForDependency(Kwf_Assets_Dependency_Abstract $dependency)
    {
        $deps = array();

        if ($dependency->getMimeType() != 'text/javascript') return $deps;

        $src = $dependency->getContentsSource();
        if ($src['type'] == 'file') {
            $fileContents = file_get_contents($src['file']);
        } else if ($src['type'] == 'contents') {
            $fileContents = $src['contents'];
        } else {
            throw new Kwf_Exception_NotYetImplemented();
        }

        // remove comments to avoid dependencies from docs/examples
        $fileContents = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*'.'/!', '', $fileContents);

        if (preg_match_all('#^\s*'.'// @require\s+([a-zA-Z0-9\./\-_]+)\s*$#m', $fileContents, $m)) {
            foreach ($m[1] as $f) {
                if (substr($f, -3) == '.js') {
                    //ignore paths
                    continue;
                }
                if ($f) {
                    $d = $this->_providerList->findDependency($f);
                    if (!$d) throw new Kwf_Exception("Can't resolve dependency: require '$f' for $dependency");
                    $deps[Kwf_Assets_Dependency_Abstract::DEPENDENCY_TYPE_REQUIRES][] = $d;
                }
            }
        }
        return $deps;
    }
}
