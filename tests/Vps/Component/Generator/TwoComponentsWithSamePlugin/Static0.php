<?php
class Vps_Component_Generator_TwoComponentsWithSamePlugin_Static0 extends Vpc_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['static1'] = array(
            'class' => 'Vps_Component_Generator_Static',
            'component' => 'Vps_Component_Generator_TwoComponentsWithSamePlugin_Static1',
        );
        $ret['generators']['static2'] = array(
            'class' => 'Vps_Component_Generator_Static',
            'component' => 'Vps_Component_Generator_TwoComponentsWithSamePlugin_Static2',
        );
        return $ret;
    }

}
