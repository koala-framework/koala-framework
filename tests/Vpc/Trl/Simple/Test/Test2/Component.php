<?php
class Vpc_Trl_Simple_Test_Test2_Component extends Vpc_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        return $ret;
    }

    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $ret['componentClass'] = get_class($this);
        return $ret;
    }
}
