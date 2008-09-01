<?php
class Vps_Component_Generator_Indirect_Flag2 extends Vpc_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['flags']['foo'] = true;
        $ret['flags']['bar'] = true;
        $ret['generators']['test'] = array(
            'class' => 'Vps_Component_Generator_Static',
            'component' => 'Vps_Component_Generator_Indirect_Flag3'
        );
        return $ret;
    }
}
?>