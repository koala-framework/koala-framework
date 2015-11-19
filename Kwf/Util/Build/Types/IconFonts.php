<?php
class Kwf_Util_Build_Types_IconFonts extends Kwf_Util_Build_Types_Abstract
{
    protected function _build()
    {
        d('A');
        d($this->getAllPackages());
    }

    public function getAllPackages()
    {
        $packages = array();
        foreach (Kwf_Config::getValueArray('assets.packageFactories') as $i) {
            if (!$i) continue;
            if (!is_instance_of($i, 'Kwf_Assets_Package_FactoryInterface')) {
                throw new Kwf_Exception("'$i' doesn't implement Kwf_Assets_Package_FactoryInterface");
            }
            $packages = array_merge($packages, call_user_func(array($i, 'createPackages')));
        }
        return $packages;
    }

    public function getTypeName()
    {
        return 'iconfonts';
    }
}
