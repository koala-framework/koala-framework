<?php
class Kwc_Trl_GetComponentByClass_Master extends Kwc_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['test1'] = array(
            'class' => 'Kwf_Component_Generator_Page_Static',
            'component' => 'Kwc_Basic_Empty_Component',
            'name' => 'test1',
        );
        $ret['flags']['chainedType'] = 'Trl';
        return $ret;
    }
}
