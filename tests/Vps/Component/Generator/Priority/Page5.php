<?php
class Vps_Component_Generator_Priority_Page5 extends Vpc_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['box5'] = array(
            'class' => 'Vps_Component_Generator_Box_Static',
            'component' => 'Vpc_Basic_Empty_Component',
            'priority' => 2,
            'box' => 'foo'
        );
        return $ret;
    }

}
