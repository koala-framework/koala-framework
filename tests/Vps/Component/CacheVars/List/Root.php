<?php
class Vps_Component_CacheVars_List_Root extends Vps_Component_NoCategoriesRoot
{
    public static function getSettings()
    {
        $ret = parent::getSettings();

        $ret['generators']['list'] = array(
            'component' => 'Vps_Component_CacheVars_List_List',
            'class' => 'Vps_Component_Generator_Static'
        );
        unset($ret['generators']['page']);
        unset($ret['generators']['title']);
        return $ret;
    }
}
