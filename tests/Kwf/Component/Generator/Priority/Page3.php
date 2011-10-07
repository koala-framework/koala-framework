<?php
class Vps_Component_Generator_Priority_Page3 extends Vpc_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['box4'] = array(
            'class' => 'Vps_Component_Generator_Box_Static',
            'component' => 'Vpc_Basic_Empty_Component',
            'inherit' => false,
            'box' => 'foo'
        );
        $ret['generators']['page4'] = array(
            'class' => 'Vps_Component_Generator_Page_Static',
            'component' => 'Vpc_Basic_Empty_Component',
            'name' => 'page4'
        );
        $ret['generators']['page5'] = array(
            'class' => 'Vps_Component_Generator_Page_Static',
            'component' => 'Vps_Component_Generator_Priority_Page5',
            'name' => 'page5'
        );
        return $ret;
    }

}
