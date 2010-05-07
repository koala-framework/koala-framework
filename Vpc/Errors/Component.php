<?php
class Vpc_Errors_Component extends Vpc_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['accessDenied'] = array(
            'class' => 'Vps_Component_Generator_Static',
            'component' => 'Vpc_Errors_AccessDenied_Component'
        );
        $ret['generators']['client'] = array(
            'class' => 'Vps_Component_Generator_Static',
            'component' => 'Vpc_Errors_Client_Component'
        );
        $ret['generators']['notFound'] = array(
            'class' => 'Vps_Component_Generator_Static',
            'component' => 'Vpc_Errors_NotFound_Component'
        );
        return $ret;
    }
}
