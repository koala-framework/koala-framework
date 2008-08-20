<?php
class Vpc_Basic_Image_View_Component extends Vpc_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['type'] = 'default';
        return $ret;
    }

    public function getTemplateVars()
    {
        $ret = $this->getData()->getParent()->getComponent()->getTemplateVars();
        $ret['type'] = $this->_getSetting('type');
        return $ret;
    }
}
