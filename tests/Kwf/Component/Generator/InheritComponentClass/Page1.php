<?php
class Kwf_Component_Generator_InheritComponentClass_Page1 extends Kwc_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['page11'] = array(
            'class' => 'Kwf_Component_Generator_Page_Static',
            'component' => 'Kwc_Basic_None_Component',
            'name' => 'page11'
        );
        return $ret;
    }

}
