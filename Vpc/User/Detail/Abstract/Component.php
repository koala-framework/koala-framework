<?php
abstract class Vpc_User_Detail_Abstract_Component extends Vpc_Abstract_Composite_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['componentName'] = '';
        return $ret;
    }
    
    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $ret['row'] = $this->getData()->parent->row;
        return $ret;
    }
}
