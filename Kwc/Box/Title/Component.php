<?php
class Kwc_Box_Title_Component extends Kwc_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['componentName'] = trlKwfStatic('Title');
        $ret['flags']['hasHeaderIncludeCode'] = true;
        return $ret;
    }

    public function getIncludeCode()
    {
        return $this->getData();
    }

    protected function _getTitle()
    {
        $ret = $this->getData()->getTitle();
        if ($ret) $ret .= ' - ';
        $ret .= $this->_getApplicationTitle();
        return $ret;
    }

    protected function _getApplicationTitle()
    {
        return Kwf_Config::getValue('application.name');
    }

    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $ret['title'] = $this->_getTitle();
        return $ret;
    }

    public static function getStaticCacheMeta($componentClass)
    {
        return Kwc_Menu_Component::getStaticCacheMeta($componentClass);
    }
}
