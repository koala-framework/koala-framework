<?php
class Kwc_Menu_ClearCacheExpanded_Root extends Kwc_Root_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['category']['component'] = 'Kwc_Menu_ClearCacheExpanded_Category_Component';
        $ret['generators']['category']['model'] = new Kwf_Model_FnF(array(
            'data' => array(
                array('id'=>'main', 'name'=>'main'),
                array('id'=>'bottom', 'name'=>'bottom'),
            )
        ));
        unset($ret['generators']['title']);
        $ret['generators']['box']['component'] = array(
            'menuMain' => 'Kwc_Menu_ClearCacheExpanded_MenuMain_Component',
            'menuSub' => 'Kwc_Menu_ClearCacheExpanded_MenuSub_Component',
        );
        return $ret;
    }
}
