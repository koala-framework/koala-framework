<?php
class Vpc_Box_Title_Component extends Vpc_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['componentName'] = trlVps('Title');
        return $ret;
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
        return Vps_Config_Web::getValue('application.name');
    }

    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $ret['title'] = $this->_getTitle();
        return $ret;
    }

    public static function getStaticCacheMeta($componentClass)
    {
        return Vpc_Menu_Component::getStaticCacheMeta($componentClass);
    }
}
