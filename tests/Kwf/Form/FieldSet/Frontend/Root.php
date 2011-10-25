<?php
class Kwf_Form_FieldSet_Frontend_Root extends Kwf_Component_NoCategoriesRoot
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['form'] = array(
            'component' => 'Kwf_Form_FieldSet_Frontend_TestForm_Component',
            'class' => 'Kwf_Component_Generator_Page_Static',
            'name' => 'form'
        );
        unset($ret['generators']['page']);
        unset($ret['generators']['title']);
        unset($ret['generators']['box']);
        return $ret;
    }
}
