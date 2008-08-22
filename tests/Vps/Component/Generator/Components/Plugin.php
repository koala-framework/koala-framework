<?php
class Vps_Component_Generator_Components_Plugin extends Vps_Component_Plugin_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['static'] = array(
            'class' => 'Vps_Component_Generator_Static',
            'component' => 'Vpc_Basic_Link_Component'
        );
        return $ret;
    }
}
?>