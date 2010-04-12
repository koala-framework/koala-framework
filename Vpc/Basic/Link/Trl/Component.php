<?php
class Vpc_Basic_Link_Trl_Component extends Vpc_Abstract_Composite_Trl_Component
{
    public static function getSettings($mainComponentClass)
    {
        $ret = parent::getSettings($mainComponentClass);
        $ret['ownModel'] = Vpc_Abstract::getSetting($mainComponentClass, 'ownModel');
        return $ret;
    }

    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $ret['text'] = $this->_getRow()->text;
        return $ret;
    }

    public function hasContent()
    {
        if (!$this->_getRow()->text) return false;
        return parent::hasContent();
    }
}
