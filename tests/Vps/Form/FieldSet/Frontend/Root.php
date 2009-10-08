<?php
class Vps_Form_FieldSet_Frontend_Root extends Vps_Component_NoCategoriesRoot
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['form'] = array(
            'component' => 'Vps_Form_FieldSet_Frontend_TestForm_Component',
            'class' => 'Vps_Component_Generator_Page_Static',
            'name' => 'form'
        );
        unset($ret['generators']['page']);
        unset($ret['generators']['title']);
        unset($ret['generators']['box']);
        return $ret;
    }
}
