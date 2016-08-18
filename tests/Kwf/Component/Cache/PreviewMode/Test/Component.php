<?php
class Kwf_Component_Cache_PreviewMode_Test_Component extends Kwc_Abstract_Composite_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        return $ret;
    }

    public function getTemplateVars(Kwf_Component_Renderer_Abstract $renderer)
    {
        $ret = parent::getTemplateVars($renderer);
        $ret['test'] = Kwf_Component_Data_Root::getShowInvisible() ? 'foo' : 'bar';
        return $ret;
    }
}
