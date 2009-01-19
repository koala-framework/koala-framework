<?php
class Vps_Component_Generator_Plugin_Static extends Vpc_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['plugins'] = array('Vps_Component_Plugin_Password_Component');
        return $ret;
    }

}
