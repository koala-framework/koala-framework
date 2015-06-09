<?php
class Kwf_Assets_Provider_ExtTrl extends Kwf_Assets_Provider_Abstract
{
    public function getDependenciesForDependency(Kwf_Assets_Dependency_Abstract $dependency)
    {
        if ($dependency instanceof Kwf_Assets_Dependency_File_Js && $dependency->getFileNameWithType() == 'ext2/src/core/EventManager.js') {
            return array(
                Kwf_Assets_Dependency_Abstract::DEPENDENCY_TYPE_USES => array(
                    new Kwf_Assets_Dependency_File_Js('kwf/Ext/ext-lang-en.js')
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
