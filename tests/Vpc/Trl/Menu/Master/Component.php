<?php
class Vpc_Trl_Menu_Master_Component extends Vpc_Root_TrlRoot_Master_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['category']['component'] = 'Vpc_Trl_Menu_Master_Category_Component';
        $ret['generators']['category']['model'] = new Vpc_Root_CategoryModel(array(
            'pageCategories' => array('main' => "Hauptmenü", 'bottom' => 'Unten')
        ));
        $ret['generators']['box']['component'] = array(
            'menu' => 'Vpc_Trl_Menu_Menu_Component',
            'levelmenu' => 'Vpc_Trl_Menu_LevelMenu_Component'
        );
        return $ret;
    }
}
