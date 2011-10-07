<?php
class Vps_Component_Generator_Recursive_Static extends Vpc_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['static2'] = array(
            'class' => 'Vps_Component_Generator_Static',
            'component' => 'Vps_Component_Generator_Recursive_Static2'
        );
        return $ret;
    }

}
