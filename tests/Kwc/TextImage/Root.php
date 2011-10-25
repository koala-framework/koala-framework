<?php
class Kwc_TextImage_Root extends Kwf_Component_NoCategoriesRoot
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['textImage1'] = array(
            'component' => 'Kwc_TextImage_TestComponent',
            'class' => 'Kwf_Component_Generator_Page_Static',
            'name' => 'TextImage1'
        );
        $ret['generators']['textImage2'] = array(
            'component' => 'Kwc_TextImage_TestComponent',
            'class' => 'Kwf_Component_Generator_Page_Static',
            'name' => 'TextImage2'
        );
        unset($ret['generators']['page']);
        unset($ret['generators']['title']);
        unset($ret['generators']['box']);
        return $ret;
    }
}
