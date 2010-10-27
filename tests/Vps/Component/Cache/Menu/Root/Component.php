<?php
class Vps_Component_Cache_Menu_Root_Component extends Vps_Component_NoCategoriesRoot
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['page']['model'] = 'Vps_Component_Cache_Menu_Root_Model';
        $ret['generators']['page']['component'] = array(
            'empty' => 'Vpc_Basic_Empty_Component',
        );
        $ret['flags']['menuCategory'] = 'root';
        $ret['generators']['box'] = array(
            'class' => 'Vps_Component_Generator_Box_Static',
            'component' => array(
                'menu' => 'Vps_Component_Cache_Menu_Root_Menu_Component'
            ),
            'inherit' => true,
            'uniqueContentLevel' => 2,
            'priority' => 0
        );
        unset($ret['generators']['title']);
        return $ret;
    }
}
