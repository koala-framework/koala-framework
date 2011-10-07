<?php
class Kwc_Trl_NewsCategories_German extends Kwc_Root_TrlRoot_Master_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators'] = array();
        $ret['generators']['test'] = array(
            'class' => 'Kwf_Component_Generator_Page_Static',
            'component' => 'Kwc_Trl_NewsCategories_News_Component',
            'name' => 'test',
        );
        return $ret;
    }
}
