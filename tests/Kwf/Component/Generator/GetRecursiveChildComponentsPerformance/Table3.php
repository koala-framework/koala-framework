<?php
class Kwf_Component_Generator_GetRecursiveChildComponentsPerformance_Table3 extends Kwc_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['flags']['processInput'] = true;

        $ret['generators']['static'] = array(
            'class' => 'Kwf_Component_Generator_Static',
            'component' => 'Kwf_Component_Generator_GetRecursiveChildComponentsPerformance_Static'
        );
        return $ret;
    }
}
