<?php
class Kwf_Component_Generator_UniqueRoot_Root extends Kwf_Component_NoCategoriesRoot
{
    public static function getSettings()
    {
        $ret = parent::getSettings();

        $ret['generators']['page']['model'] = new Kwf_Model_FnF();
        $ret['generators']['page']['component'] = array();
        unset($ret['generators']['box']);
        unset($ret['generators']['title']);

        $ret['generators']['page2'] = array(
            'class' => 'Kwf_Component_Generator_Page_Static',
            'component' => 'Kwc_Basic_Empty_Component',
            'name' => 'page1'
        );

        $ret['generators']['box'] = array(
            'class' => 'Kwf_Component_Generator_Box_Static',
            'component' => 'Kwc_Basic_Empty_Component',
            'unique' => true,
            'inherit' => true
        );
        return $ret;
    }
}
