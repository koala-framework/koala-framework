<?php
class Vpc_ShowContent_Component extends Vpc_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['foo'] = 'bar';
        return $ret;
    }
    
    public function getTemplateVars()
    {
        $vars = parent::getTemplateVars();
        $vars['componentId'] = $this->getData()->id;
        return $vars;
    }
}
