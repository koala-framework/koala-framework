<?php
class Vps_Component_Cache_Directory_Root_Component extends Vps_Component_NoCategoriesRoot
{
    public static function getSettings()
    {
        $ret = parent::getSettings();

        $ret['generators']['dir'] = array(
            'component' => 'Vps_Component_Cache_Directory_Root_Directory_Component',
            'class' => 'Vps_Component_Generator_Page_Static'
        );
        $ret['generators']['list'] = array(
            'component' => 'Vps_Component_Cache_Directory_Root_List_Component',
            'class' => 'Vps_Component_Generator_Page_Static'
        );
        unset($ret['generators']['page']);
        unset($ret['generators']['title']);
        return $ret;
    }
}
