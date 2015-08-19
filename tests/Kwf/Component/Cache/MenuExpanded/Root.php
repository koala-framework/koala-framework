<?php
class Kwf_Component_Cache_MenuExpanded_Root extends Kwf_Component_NoCategoriesRoot
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['page']['model'] = 'Kwf_Component_Cache_MenuExpanded_PagesModel';
        $ret['generators']['page']['historyModel'] = new Kwf_Model_FnF();
        $ret['generators']['page']['component'] = array(
            'empty' => 'Kwc_Basic_Empty_Component',
        );
        $ret['flags']['menuCategory'] = 'root';
        $ret['generators']['box'] = array(
            'class' => 'Kwf_Component_Generator_Box_Static',
            'component' => array(
                'menu' => 'Kwf_Component_Cache_MenuExpanded_Menu_Component'
            ),
            'inherit' => true,
            'priority' => 0
        );
        unset($ret['generators']['title']);
        return $ret;
    }
}
