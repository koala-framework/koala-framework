<?php
class Kwf_Component_Generator_InheritComponentClass_Root extends Kwf_Component_NoCategoriesRoot
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['page']['model'] = new Kwf_Model_FnF();

        $ret['generators']['page1'] = array(
            'class' => 'Kwf_Component_Generator_Page_Static',
            'component' => 'Kwf_Component_Generator_InheritComponentClass_Page1',
            'name' => 'page1'
        );
        $ret['generators']['page2'] = array(
            'class' => 'Kwf_Component_Generator_Page_Static',
            'component' => 'Kwf_Component_Generator_InheritComponentClass_Page2',
            'name' => 'page2'
        );

        $ret['generators']['box']['component']['box1'] = 'Kwc_Basic_Empty_Component';
        unset($ret['generators']['title']);
        return $ret;
    }

}
