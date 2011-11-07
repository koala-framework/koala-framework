<?php
class Kwf_Component_Generator_GetRecursiveChildComponentsWithAlternativeComponent_TestComponent2_Component extends Kwc_Abstract_Composite_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['child']['component']['test2'] = 'Kwc_Basic_Empty_Component';
        return $ret;
    }
}
