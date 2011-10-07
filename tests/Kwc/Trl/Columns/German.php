<?php
class Kwc_Trl_Columns_German extends Kwc_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['test'] = array(
            'class' => 'Kwf_Component_Generator_Page_Static',
            'component' => 'Kwc_Trl_Columns_Columns_Component',
            'name' => 'test',
        );
        return $ret;
    }
}
