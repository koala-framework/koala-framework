<?php
class Kwc_Basic_Image_TestComponent extends Kwc_Basic_Image_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['ownModel'] = 'Kwc_Basic_Image_TestModel';
        $ret['dimensions'] = array(array(
            'width'=>Kwc_Abstract_Image_Component::USER_SELECT,
            'height'=>Kwc_Abstract_Image_Component::USER_SELECT,
            'scale'=>Kwf_Media_Image::SCALE_DEFORM,
        ));
        return $ret;
    }
}
