<?php
class Vpc_Trl_NewsCategories_German extends Vpc_Root_TrlRoot_Master_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators'] = array();
        $ret['generators']['test'] = array(
            'class' => 'Vps_Component_Generator_Page_Static',
            'component' => 'Vpc_Trl_NewsCategories_News_Component',
            'name' => 'test',
        );
        return $ret;
    }
}
