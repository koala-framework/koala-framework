<?php
class Vps_Component_Generator_Page_Child extends Vpc_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['foo'] = array(
            'class' => 'Vps_Component_Generator_Static',
            'component' => 'Vpc_Basic_Empty_Component'
        );
        return $ret;
    }
}
?>