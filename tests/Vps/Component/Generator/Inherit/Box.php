<?php
class Vps_Component_Generator_Inherit_Box extends Vpc_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['flag'] = array(
            'class' => 'Vps_Component_Generator_Static',
            'component' => 'Vps_Component_Generator_Inherit_Flag'
        );
        return $ret;
    }

}
