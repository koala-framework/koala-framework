<?php
class Vpc_TextImage_Root extends Vps_Component_NoCategoriesRoot
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['textImage1'] = array(
            'component' => 'Vpc_TextImage_TestComponent',
            'class' => 'Vps_Component_Generator_Page_Static',
            'name' => 'TextImage1'
        );
        $ret['generators']['textImage2'] = array(
            'component' => 'Vpc_TextImage_TestComponent',
            'class' => 'Vps_Component_Generator_Page_Static',
            'name' => 'TextImage2'
        );
        unset($ret['generators']['page']);
        unset($ret['generators']['title']);
        unset($ret['generators']['box']);
        return $ret;
    }
}
