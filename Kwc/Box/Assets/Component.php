<?php
class Kwc_Box_Assets_Component extends Kwc_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['flags']['hasHeaderIncludeCode'] = true;
        return $ret;
    }

    public function getIncludeCode()
    {
        return $this->getData();
    }

    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $ret['language'] = $this->getData()->getLanguage();
        $ret['assetsPackage'] = new Kwf_Assets_Dependency_Package(new Kwf_Assets_ProviderList_Default(), 'Frontend');
        return $ret;
    }

    /**
     * @deprecated
     */
    protected final function _getSection()
    {
    }
}
