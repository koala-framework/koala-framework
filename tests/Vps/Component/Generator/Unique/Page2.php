<?php
class Vps_Component_Generator_Unique_Page2 extends Vpc_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['box2'] = array(
            'class' => 'Vps_Component_Generator_Box_Static',
            'component' => 'Vpc_Basic_Empty_Component',
            'unique' => true,
            'inherit' => true,
            'priority' => 3,
            'box' => 'box'
        );
        $ret['generators']['page3'] = array(
            'class' => 'Vps_Component_Generator_Page_Static',
            'component' => 'Vpc_Basic_Empty_Component',
            'name' => 'page3'
        );
        return $ret;
    }

}
