<?php
class Kwc_Trl_Menu_Master_Component extends Kwc_Root_TrlRoot_Master_Component
{
    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);
        $ret['generators']['category']['component'] = 'Kwc_Trl_Menu_Master_Category_Component';
        $ret['generators']['category']['model'] = new Kwc_Root_CategoryModel(array(
            'pageCategories' => array('main' => "Hauptmenü", 'bottom' => 'Unten')
        ));
        $ret['generators']['box']['component'] = array(
            'menu' => 'Kwc_Trl_Menu_Menu_Component',
            'levelmenu' => 'Kwc_Trl_Menu_LevelMenu_Component'
        );
        return $ret;
    }
}
