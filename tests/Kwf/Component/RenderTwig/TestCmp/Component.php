<?php
class Kwf_Component_RenderTwig_TestCmp_Component extends Kwc_Abstract_Composite_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['child']['component']['child'] = 'Kwf_Component_RenderTwig_TestCmp_Child_Component';
        return $ret;
    }
}
