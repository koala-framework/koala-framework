<?php
class Vps_Component_Generator_AlternativeComponent_Component extends Vpc_Abstract_Composite_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['child']['component']['child'] = 'Vps_Component_Generator_AlternativeComponent_Default_Component';
        return $ret;
    }
}
