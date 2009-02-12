<?php
class Vps_Component_CacheVars_Table_Root extends Vps_Component_NoCategoriesRoot
{
    public static function getSettings()
    {
        $ret = parent::getSettings();

        $ret['generators']['table'] = array(
            'component' => 'Vpc_Basic_Empty_Component',
            'class' => 'Vps_Component_Generator_Table',
            'model' => 'Vps_Component_CacheVars_Table_Model'
        );
        unset($ret['generators']['page']);
        unset($ret['generators']['title']);
        return $ret;
    }
}
