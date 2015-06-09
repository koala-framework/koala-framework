<?php
class Kwf_Assets_ModuleDeps_TestJsClassProvider extends Kwf_Assets_Provider_JsClass
{
    public function getDependency($dependencyName)
    {
        $ret = parent::getDependency($dependencyName);
        if ($dependencyName == 'Kwf.Assets.ModuleDeps.Test' || $dependencyName == 'Kwf.Assets.ModuleDeps.A' || $dependencyName == 'Kwf.Assets.ModuleDeps.C') {
            $ret->setIsCommonJsEntry(true);
        }
        return $ret;
    }
}
