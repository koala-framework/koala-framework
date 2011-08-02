<?php
class Vpc_Js_Event_Root extends Vpc_Root_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['js'] = array(
            'class' => 'Vps_Component_Generator_Page_Static',
            'name' => 'js',
            'component' => 'Vpc_Js_Event_TestComponent_Component'
        );
        return $ret;
    }
}


