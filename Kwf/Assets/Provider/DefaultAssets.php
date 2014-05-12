<?php
class Kwf_Assets_Provider_DefaultAssets extends Kwf_Assets_Provider_Abstract
{
    public function getDependency($dependencyName)
    {
        return null;
    }

    public function getDefaultDependencies()
    {
        return array(
            new Kwf_Assets_Dependency_File_Js('kwf/Kwf_js/AssetsVersion.js'),
            new Kwf_Assets_Dependency_File_Js('kwf/Kwf_js/Trl.js'),
        );
    }
}
