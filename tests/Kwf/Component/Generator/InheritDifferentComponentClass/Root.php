<?php
class Kwf_Component_Generator_InheritDifferentComponentClass_Root extends Kwf_Component_NoCategoriesRoot
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        unset($ret['generators']['page']);

        $ret['generators']['page1'] = array(
            'class' => 'Kwf_Component_Generator_Page_Static',
            'component' => 'Kwf_Component_Generator_InheritDifferentComponentClass_Page1',
            'name' => 'page1'
        );

        $ret['generators']['box']['component']['box'] = 'Kwf_Component_Generator_InheritDifferentComponentClass_Box_Component';
        unset($ret['generators']['title']);
        return $ret;
    }

}
