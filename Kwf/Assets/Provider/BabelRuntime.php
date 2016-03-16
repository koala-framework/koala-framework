<?php
class Kwf_Assets_Provider_BabelRuntime extends Kwf_Assets_Provider_Abstract
{
    public function getDependency($dependencyName)
    {
        if (substr($dependencyName, 0, 14) == 'babel-runtime/' || substr($dependencyName, 0, 8) == 'core-js/') {
            return new Kwf_Assets_Dependency_File_Js($this->_providerList, $dependencyName.'.js');
        }
        return null;
    }

    public function getPathTypes()
    {
        return array(
            'babel-runtime' => KWF_PATH.'/node_modules/babel-runtime',
            'core-js' => KWF_PATH.'/node_modules/core-js',
        );
    }
}
