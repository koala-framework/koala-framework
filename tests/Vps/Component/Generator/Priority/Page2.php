<?php
class Vps_Component_Generator_Priority_Page2 extends Vpc_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['box3'] = array(
            'class' => 'Vps_Component_Generator_Box_Static',
            'component' => 'Vpc_Basic_Empty_Component',
            'inherit' => false,
            'box' => 'foo'
        );
        return $ret;
    }

}
