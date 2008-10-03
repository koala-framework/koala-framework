<?php
class Vpc_Box_InheritContent_Page1 extends Vpc_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['page2'] = array(
            'class' => 'Vps_Component_Generator_Page_Static',
            'component' => 'Vpc_Box_InheritContent_Page2',
            'name' => 'page2'
        );
        return $ret;
    }

}
