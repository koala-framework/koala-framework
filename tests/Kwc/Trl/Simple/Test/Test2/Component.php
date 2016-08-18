<?php
class Kwc_Trl_Simple_Test_Test2_Component extends Kwc_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        return $ret;
    }

    public function getTemplateVars(Kwf_Component_Renderer_Abstract $renderer)
    {
        $ret = parent::getTemplateVars($renderer);
        $ret['componentClass'] = get_class($this);
        return $ret;
    }
}
