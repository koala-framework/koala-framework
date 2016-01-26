<?php
class Kwf_Assets_Provider_BabelRuntime extends Kwf_Assets_Provider_Abstract
{
    public function getDependency($dependencyName)
    {
        if (substr($dependencyName, 0, 14) == 'babel-runtime/' || substr($dependencyName, 0, 8) == 'core-js/') {
            return new Kwf_Assets_Dependency_File_Js('kwf/node_modules/'.$dependencyName.'.js');
        }
        return null;
    }
}
