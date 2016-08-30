<?php
class Kwc_Cc_Composite_Master_Component extends Kwc_Abstract
{
    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);
        $ret['generators']['composite'] = array(
            'class' => 'Kwf_Component_Generator_Page_Static',
            'component' => 'Kwc_Cc_Composite_Master_Composite_Component',
        );
        $ret['flags']['chainedType'] = 'Cc';
        return $ret;
    }
}