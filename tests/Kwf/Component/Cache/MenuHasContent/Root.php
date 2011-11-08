<?php
class Kwf_Component_Cache_MenuHasContent_Root extends Kwc_Root_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['category']['component'] = 'Kwf_Component_Cache_MenuHasContent_Category_Component';
        $ret['generators']['category']['model'] = new Kwf_Model_FnF(array(
            'data' => array(
                array('id'=>'top', 'name'=>'top'),
                array('id'=>'main', 'name'=>'main'),
            )
        ));
        unset($ret['generators']['title']);
        $ret['generators']['box']['component'] = array(
            'menuMain' => 'Kwf_Component_Cache_MenuHasContent_MenuMain_Component',
            'menuTop' => 'Kwf_Component_Cache_MenuHasContent_MenuTop_Component',
        );
        return $ret;
    }
}
