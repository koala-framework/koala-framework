<?php
class Kwf_Component_RenderTwig_TestCmp_Component extends Kwc_Abstract_Composite_Component
{
    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);
        $ret['generators']['child']['component']['child'] = 'Kwf_Component_RenderTwig_TestCmp_Child_Component';
        return $ret;
    }
}
