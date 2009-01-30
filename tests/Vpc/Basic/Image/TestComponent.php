<?php
class Vpc_Basic_Image_TestComponent extends Vpc_Basic_Image_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['modelname'] = 'Vpc_Basic_Image_TestModel';
        $ret['dimensions'] = array(array(
            'width'=>Vpc_Abstract_Image_Component::USER_SELECT,
            'height'=>Vpc_Abstract_Image_Component::USER_SELECT,
            'scale'=>Vpc_Abstract_Image_Component::USER_SELECT,
        ));
        return $ret;
    }
}
