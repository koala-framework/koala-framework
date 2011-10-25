<?php
class Kwf_Component_Generator_AlternativeComponent_Component extends Kwc_Abstract_Composite_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['child']['component']['child'] = 'Kwf_Component_Generator_AlternativeComponent_Default_Component';
        return $ret;
    }
}
