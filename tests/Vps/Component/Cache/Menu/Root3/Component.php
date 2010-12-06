<?php
class Vps_Component_Cache_Menu_Root3_Component extends Vps_Component_NoCategoriesRoot
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['page']['model'] = 'Vps_Component_Cache_Menu_Root3_Model';
        $ret['generators']['page']['component'] = array(
            'menu1' => 'Vps_Component_Cache_Menu_Root3_Menu1_Component',
            'menu2' => 'Vps_Component_Cache_Menu_Root3_Menu2_Component',
            'menu3' => 'Vps_Component_Cache_Menu_Root3_Menu3_Component',
        );
        $ret['flags']['menuCategory'] = 'root';
        return $ret;
    }
}
