<?php
class Vpc_Box_DogearRandom_Component extends Vpc_Abstract_ListRandom_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['componentName'] = trlVps('Dogears');
        $ret['generators']['child']['component'] = 'Vpc_Box_DogearRandom_Dogear_Component';
        return $ret;
    }
}
