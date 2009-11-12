<?php
class Vps_Component_CacheVars_Menu_Root extends Vps_Component_NoCategoriesRoot
{
    public static function getSettings()
    {
        $ret = parent::getSettings();

        $ret['generators']['page']['model'] = 'Vps_Component_CacheVars_Menu_PageModel';
        $ret['generators']['page']['component'] = array('empty' => 'Vpc_Basic_Empty_Component');

        $ret['generators']['children'] = array(
            'component' => 'Vps_Component_CacheVars_Menu_Component',
            'class' => 'Vps_Component_Generator_Page_Table',
            'showInMenu' => true,
            'model' => 'Vps_Component_CacheVars_Menu_Model'
        );

        $ret['generators']['menu'] = array(
            'component' => 'Vpc_Menu_Component',
            'class' => 'Vps_Component_Generator_Box_Static',
            'inherit' => true
        );

        unset($ret['generators']['title']);
        return $ret;
    }
}
