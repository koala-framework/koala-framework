<?php
class Kwc_Trl_Headlines_German extends Kwc_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['headlines'] = array(
            'class' => 'Kwf_Component_Generator_Page_Static',
            'component' => 'Kwc_Trl_Headlines_Headlines_Component',
            'name' => 'headlines',
        );
        return $ret;
    }
}
