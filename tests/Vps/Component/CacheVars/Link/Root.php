<?php
class Vps_Component_CacheVars_Link_Root extends Vps_Component_NoCategoriesRoot
{
    public static function getSettings()
    {
        $ret = parent::getSettings();

        $ret['generators']['link'] = array(
            'component' => 'Vps_Component_CacheVars_Link_Link',
            'class' => 'Vps_Component_Generator_Page_Static'
        );

        $ret['generators']['menu'] = array(
            'component' => 'Vpc_Menu_Component',
            'class' => 'Vps_Component_Generator_Box_Static',
            'inherit' => true
        );

        unset($ret['generators']['page']);
        return $ret;
    }
}
