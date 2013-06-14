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
        $ret['section'] = $this->_getSection();
        $ret['language'] = $this->getData()->getLanguage();
        return $ret;
    }

    protected function _getSection()
    {
        return 'web';
    }
}
