<?php
class Kwf_Component_Cache_Menu_Root2_Component extends Kwf_Component_NoCategoriesRoot
{
    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);
        $ret['generators']['page']['model'] = 'Kwf_Component_Cache_Menu_Root2_Model';
        $ret['generators']['page']['historyModel'] = new Kwf_Model_FnF();
        $ret['generators']['page']['component'] = array(
            'empty' => 'Kwc_Basic_Empty_Component',
        );
        $ret['generators']['box'] = array(
            'class' => 'Kwf_Component_Generator_Box_Static',
            'component' => array(
                'menu' => 'Kwf_Component_Cache_Menu_Root2_Menu_Component'
            ),
            'inherit' => true,
            'priority' => 0
        );
        $ret['flags']['menuCategory'] = 'root';
        return $ret;
    }
}
