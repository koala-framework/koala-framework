<?php
class Kwc_Basic_Image_Crop_ImageUserSelectComponent extends Kwc_Basic_Image_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['ownModel'] = 'Kwc_Basic_Image_Crop_TestModel';
        $ret['dimensions'] = array(
            array(
                'width'=>Kwc_Abstract_Image_Component::USER_SELECT,
                'height'=>Kwc_Abstract_Image_Component::USER_SELECT,
                'bestfit' => true,
            )
        );
        return $ret;
    }
}
