<?php
class Kwf_Component_Generator_AlternativeComponent_Component extends Kwc_Abstract_Composite_Component
{
    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);
        $ret['generators']['child']['component']['child'] = 'Kwf_Component_Generator_AlternativeComponent_Default_Component';
        return $ret;
    }
}
