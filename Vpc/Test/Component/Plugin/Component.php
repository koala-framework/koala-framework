<?php
class Vpc_Test_Component_Plugin_Component extends Vps_Component_Plugin_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['static'] = array(
            'class' => 'Vps_Component_Generator_Static',
            'component' => 'Vpc_Basic_Empty_Component'
        );
        return $ret;
    }
}
?>