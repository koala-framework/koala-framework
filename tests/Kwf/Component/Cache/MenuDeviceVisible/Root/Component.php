<?php
class Kwf_Component_Cache_MenuDeviceVisible_Root_Component extends Kwf_Component_NoCategoriesRoot
{
    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);
        $ret['generators']['page']['class'] = 'Kwf_Component_Cache_MenuDeviceVisible_Root_CategoryGenerator';
        $ret['generators']['page']['model'] = 'Kwf_Component_Cache_MenuDeviceVisible_Root_Model';
        $ret['generators']['page']['component'] = array(
            'empty' => 'Kwc_Basic_Empty_Component',
        );
        $ret['flags']['menuCategory'] = 'root';
        $ret['generators']['box'] = array(
            'class' => 'Kwf_Component_Generator_Box_Static',
            'component' => array(
                'menu' => 'Kwf_Component_Cache_MenuDeviceVisible_Root_Menu_Component'
            ),
            'inherit' => true,
            'priority' => 0
        );
        unset($ret['generators']['title']);
        return $ret;
    }
}
