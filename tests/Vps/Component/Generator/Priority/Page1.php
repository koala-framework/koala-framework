<?php
class Vps_Component_Generator_Priority_Page1 extends Vpc_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['box2'] = array(
            'class' => 'Vps_Component_Generator_Box_Static',
            'component' => 'Vpc_Basic_Empty_Component',
            'inherit' => true,
            'box' => 'foo'
        );
        $ret['generators']['page2'] = array(
            'class' => 'Vps_Component_Generator_Page_Static',
            'component' => 'Vps_Component_Generator_Priority_Page2',
            'name' => 'page2'
        );
        $ret['generators']['page3'] = array(
            'class' => 'Vps_Component_Generator_Page_Static',
            'component' => 'Vps_Component_Generator_Priority_Page3',
            'name' => 'page3'
        );
        return $ret;
    }

}
