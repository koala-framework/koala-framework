<?php
class Kwf_Assets_Provider_JsClassKwf extends Kwf_Assets_Provider_Abstract
{
    public function getDependency($dependencyName)
    {
        if (substr($dependencyName, 0, 4) != 'Kwf.') {
            return null;
        }
        $d = '/Kwf_js/'.str_replace('.', '/', substr($dependencyName, 4)).'.js';
        if (file_exists(KWF_PATH.$d)) {
            return Kwf_Assets_Dependency_File::createDependency('kwf'.$d, $this->_providerList);
        }
        return null;
    }

    public function getDependenciesForDependency(Kwf_Assets_Dependency_Abstract $dependency)
    {
        if (!$dependency instanceof Kwf_Assets_Dependency_File_Js) {
            return array();
        }

        if ($dependency->getFileNameWithType() == 'kwf/Kwf_js/Loader.js') {
            return array(
                Kwf_Assets_Dependency_Abstract::DEPENDENCY_TYPE_REQUIRES => array(
                    new Kwf_Assets_Dependency_Dynamic_LoaderConfig($this->_providerList)
                )
            );
        }

        $fileContents = $dependency->getContentsSourceString();

        if (strpos($fileContents, 'Kwf.Loader.') === false) {
            //shortcut
            return array();
        }

        // remove comments to avoid dependencies from docs/examples
        $fileContents = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*'.'/!', '', $fileContents);

        if (preg_match('#Kwf\.Loader\.#', $fileContents)) {
            $d = $this->_providerList->findDependency('Kwf.Loader');
            if (!$d) {
                throw new Kwf_Exception("Can't find dependency 'Kwf.Loader'");
            }
            return array(
                Kwf_Assets_Dependency_Abstract::DEPENDENCY_TYPE_REQUIRES => array($d)
            );
        }
        return array();
    }
}
