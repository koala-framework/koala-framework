<?php
class Vps_Component_Cache_Menu_Root2_Component extends Vps_Component_NoCategoriesRoot
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['page']['model'] = 'Vps_Component_Cache_Menu_Root2_Model';
        $ret['generators']['page']['component'] = array(
            'empty' => 'Vpc_Basic_Empty_Component',
        );
        $ret['generators']['box'] = array(
            'class' => 'Vps_Component_Generator_Box_Static',
            'component' => array(
                'menu' => 'Vps_Component_Cache_Menu_Root2_Menu_Component'
            ),
            'inherit' => true,
            'uniqueContentLevel' => 2,
            'priority' => 0
        );
        $ret['flags']['menuCategory'] = 'root';
        return $ret;
    }
}
