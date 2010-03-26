<?php
class Vpc_Menu_Root extends Vps_Component_NoCategoriesRoot
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['page']['model'] = 'Vpc_Menu_PagesModel';
        $ret['generators']['page']['component'] = array(
            'empty' => 'Vpc_Basic_Empty_Component'
        );
        $ret['generators']['box']['component']['menu'] = 'Vpc_Menu_Menu_Component';
        $ret['generators']['box']['component']['levelmenu'] = 'Vpc_Menu_LevelMenu_Component';
        unset($ret['generators']['title']);
        $ret['flags']['menuCategory'] = true;
        return $ret;
    }
}
