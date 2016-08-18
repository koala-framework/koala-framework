<?php
class Kwf_Component_Cache_Menu_Root3_Component extends Kwf_Component_NoCategoriesRoot
{
    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);
        $ret['generators']['page']['model'] = 'Kwf_Component_Cache_Menu_Root3_Model';
        $ret['generators']['page']['historyModel'] = new Kwf_Model_FnF();
        $ret['generators']['page']['component'] = array(
            'empty' => 'Kwc_Basic_Empty_Component',
        );
        $ret['generators']['menus'] = array(
            'class' => 'Kwf_Component_Generator_Box_Static',
            'inherit' => true,
            'component' => array(
                'menu1' => 'Kwf_Component_Cache_Menu_Root3_Menu1_Component',
                'menu2' => 'Kwf_Component_Cache_Menu_Root3_Menu2_Component',
                'menu3' => 'Kwf_Component_Cache_Menu_Root3_Menu3_Component',
            )
        );
        $ret['flags']['menuCategory'] = 'root';
        return $ret;
    }
}
