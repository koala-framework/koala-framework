<?php
class Vps_Component_Generator_Components_RecursiveStatic extends Vpc_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['static'] = array(
            'class' => 'Vps_Component_Generator_Box_Static',
            'component' => 'Vps_Component_Generator_Components_RecursiveStatic'
        );
        return $ret;
    }
}
?>