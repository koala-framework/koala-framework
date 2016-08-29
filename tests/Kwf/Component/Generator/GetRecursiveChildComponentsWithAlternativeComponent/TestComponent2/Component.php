<?php
class Kwf_Component_Generator_GetRecursiveChildComponentsWithAlternativeComponent_TestComponent2_Component extends Kwc_Abstract_Composite_Component
{
    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);
        $ret['generators']['child']['component']['test2'] = 'Kwc_Basic_None_Component';
        return $ret;
    }
}
