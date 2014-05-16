<?php
class Kwf_Assets_Ext4_Extensible_Provider extends Kwf_Assets_Provider_Abstract
{
    public function getDependency($dependencyName)
    {
        if (substr($dependencyName, 0, 11) == 'Extensible.' || $dependencyName == 'Extensible') {
            $class = $dependencyName;
            if ($class != 'Extensible') {
                $class = substr($class, 11);
            }
            $file = 'extensible/src/'.str_replace('.', '/', $class).'.js';
            return new Kwf_Assets_Ext4_Extensible_JsDependency($file);
        }
        return null;
    }

    public function getDependenciesForDependency(Kwf_Assets_Dependency_Abstract $dependency)
    {
        if ($dependency instanceof Kwf_Assets_Ext4_Extensible_JsDependency && $dependency->getFileNameWithType() == 'extensible/src/data/Model.js') {
            //automatically load core dependencies
            return array(
                Kwf_Assets_Dependency_Abstract::DEPENDENCY_TYPE_REQUIRES => array(
                    $this->_providerList->findDependency('Extensible'),
                    new Kwf_Assets_Ext4_Extensible_CssDependency('extensible/resources/css/extensible-all.css')
                ),
            );
        }
        return array();
    }
}
