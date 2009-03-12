<?php
class Vpc_Errors_Component extends Vpc_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['accessDenied'] = array(
            'class' => 'Vps_Component_Generator_Page_Static',
            'component' => 'Vpc_Errors_AccessDenied_Component',
            'name' => 'Access Denied'
        );
        $ret['generators']['client'] = array(
            'class' => 'Vps_Component_Generator_Page_Static',
            'component' => 'Vpc_Errors_Client_Component',
            'name' => 'Client Error'
        );
        $ret['generators']['notFound'] = array(
            'class' => 'Vps_Component_Generator_Page_Static',
            'component' => 'Vpc_Errors_NotFound_Component',
            'name' => 'Not Found'
        );
        return $ret;
    }
}
