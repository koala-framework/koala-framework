<?php
class Kwf_Component_Generator_ShowInMenu_Root extends Kwf_Component_NoCategoriesRoot
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['page']['model'] = new Kwf_Model_FnF();
        $ret['generators']['page']['component'] = array();
        unset($ret['generators']['title']);
        unset($ret['generators']['box']);
        $ret['generators']['page2'] = array(
            'class' => 'Kwf_Component_Generator_Page_Static',
            'component' => 'Kwc_Basic_None_Component',
            'name' => 'page2',
            'showInMenu' => true
        );
        $ret['generators']['page3'] = array(
            'class' => 'Kwf_Component_Generator_Page_Static',
            'component' => 'Kwc_Basic_None_Component',
            'name' => 'page3',
            'showInMenu' => false
        );
        return $ret;
    }
}
