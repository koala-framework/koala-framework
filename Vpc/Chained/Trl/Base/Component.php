<?php
class Vpc_Chained_Trl_Base_Component extends Vpc_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['chained'] = array(
            'class' => 'Vpc_Chained_Trl_Base_Generator',
            'component' => 'Vpc_Chained_Trl_Component'
        );
        return $ret;
    }
}
