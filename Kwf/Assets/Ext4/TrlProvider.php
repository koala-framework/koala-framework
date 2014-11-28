<?php
class Kwf_Assets_Ext4_TrlProvider extends Kwf_Assets_Provider_Abstract
{
    public function getDependenciesForDependency(Kwf_Assets_Dependency_Abstract $dependency)
    {
        if ($dependency instanceof Kwf_Assets_Ext4_JsDependency && $dependency->getFileName() == Kwf_Config::getValue('path.ext4').'/src/Ext.js') {
            return array(
                Kwf_Assets_Dependency_Abstract::DEPENDENCY_TYPE_USES => array(
                    new Kwf_Assets_Dependency_File_Js('kwf/Kwf_js/Ext4/ext-lang-en.js')
                )
            );
        } else if ($dependency instanceof Kwf_Assets_Ext4_Extensible_JsDependency && $dependency->getFileName() == Kwf_Config::getValue('path.extensible').'/src/Extensible.js') {
            return array(
                Kwf_Assets_Dependency_Abstract::DEPENDENCY_TYPE_USES => array(
                    new Kwf_Assets_Dependency_File_Js('kwf/Kwf_js/Ext4/extensible-lang-en.js')
                )
            );
        }
        return array();
    }

    public function getDependency($dependencyName)
    {
        return null;
    }
}
