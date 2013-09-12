<?php
class Kwf_Component_Cache_Menu_Root4_Component extends Kwf_Component_NoCategoriesRoot
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        unset($ret['generators']['page']);
        $ret['generators']['page'] = array(
            'component' => 'Kwf_Component_Cache_Menu_Root4_Page_Component',
            'class' => 'Kwf_Component_Generator_Page_Static',
        );
        $ret['generators']['box'] = array(
            'class' => 'Kwf_Component_Generator_Box_Static',
            'component' => array(
                //'menu' => 'Kwf_Component_Cache_Menu_Root4_Menu_Component',
                'submenu' => 'Kwf_Component_Cache_Menu_Root4_Submenu_Component'
            ),
            'inherit' => true,
            'priority' => 0
        );
        unset($ret['generators']['title']);
        $ret['flags']['menuCategory'] = 'root';
        return $ret;
    }
}
