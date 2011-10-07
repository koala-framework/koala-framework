<?php
class Kwc_Box_DogearRandom_Component extends Kwc_Abstract_ListRandom_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['componentName'] = trlKwf('Dogears');
        $ret['generators']['child']['component'] = 'Kwc_Box_DogearRandom_Dogear_Component';
        return $ret;
    }
}
