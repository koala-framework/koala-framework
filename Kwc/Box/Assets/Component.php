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
        $ret['assetsPackage'] = Kwf_Assets_Package_Default::getInstance('Frontend');
        return $ret;
    }

    /**
     * @deprecated
     */
    protected final function _getSection()
    {
    }
}
