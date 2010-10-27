<?php
class Vps_Component_Generator_InheritComponentClass_Page2 extends Vpc_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['box21'] = array(
            'class' => 'Vps_Component_Generator_Box_Static',
            'component' => 'Vpc_Basic_Empty_Component',
            'box' => 'box1',
            'priority' => 3,
            'inherit' => true
        );
        $ret['generators']['page21'] = array(
            'class' => 'Vps_Component_Generator_Page_Static',
            'component' => 'Vpc_Basic_Empty_Component',
            'name' => 'page21'
        );
        $ret['generators']['comp21'] = array(
            'class' => 'Vps_Component_Generator_Static',
            'component' => 'Vpc_Basic_Empty_Component'
        );
        return $ret;
    }

}
