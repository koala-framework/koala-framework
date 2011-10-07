<?php
class Vps_Component_Generator_GetRecursiveChildComponentsPerformance_Table3 extends Vpc_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['flags']['processInput'] = true;

        $ret['generators']['static'] = array(
            'class' => 'Vps_Component_Generator_Static',
            'component' => 'Vps_Component_Generator_GetRecursiveChildComponentsPerformance_Static'
        );
        return $ret;
    }
}
