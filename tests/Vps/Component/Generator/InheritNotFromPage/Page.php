<?php
class Vps_Component_Generator_InheritNotFromPage_Page extends Vpc_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['child'] = array(
            'class' => 'Vps_Component_Generator_Static',
            'component' => 'Vps_Component_Generator_InheritNotFromPage_PageChild'
        );
        return $ret;
    }
}
