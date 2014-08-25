<?php
class Kwf_Assets_Provider_NoComponents extends Kwf_Assets_Provider_Abstract
{
    public function getDependency($dependencyName)
    {
        if ($dependencyName == 'Components' || $dependencyName == 'ComponentsAdmin') {
            return new Kwf_Assets_Dependency_Dependencies(array(), $dependencyName);
        }
        return null;
    }
}
