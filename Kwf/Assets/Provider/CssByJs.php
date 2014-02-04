<?php
class Kwf_Assets_Provider_CssByJs extends Kwf_Assets_Provider_Abstract
{
    public function getDependenciesForDependency(Kwf_Assets_Dependency_Abstract $dependency)
    {
        $ret = array();
        if ($dependency->getMimeType() == 'text/javascript' && $dependency instanceof Kwf_Assets_Dependency_File) {
            $fn = $dependency->getFileName();
            if (substr($fn, -3) == '.js') {
                $fn = substr($fn, 0, -3).'.css';
                if (file_exists($fn)) {
                    $ret[Kwf_Assets_Dependency_Abstract::DEPENDENCY_TYPE_REQUIRES][] = new Kwf_Assets_Dependency_File_Css($fn);
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
