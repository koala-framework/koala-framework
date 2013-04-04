<?php
class Kwc_Trl_Table_Root_German extends Kwc_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['table'] = array(
            'class' => 'Kwf_Component_Generator_Page_Static',
            'component' => 'Kwc_Trl_Table_Table_Component',
            'name' => 'table',
        );
        return $ret;
    }
}
