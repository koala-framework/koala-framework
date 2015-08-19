<?php
class Kwc_Box_SwitchLanguage_Meta_Component extends Kwc_Abstract
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
        $ret['languages'] = $this->getData()->parent->getComponent()->getLanguages(false);
        return $ret;
    }
}
