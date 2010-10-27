<?php
class Vps_Component_Generator_UniqueLevel_Test_Component extends Vpc_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['box'] = array(
            'class' => 'Vps_Component_Generator_Box_Static',
            'component' => array(
                'menu' => 'Vpc_Basic_Empty_Component'
            ),
            'inherit' => true,
            'uniqueContentLevel' => 3,
            'priority' => 0
        );
        return $ret;
    }
}
