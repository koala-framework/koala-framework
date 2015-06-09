<?php
class Kwc_Trl_Image_Image_Component extends Kwc_Basic_Image_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['ownModel'] = 'Kwc_Trl_Image_Image_TestModel';

        $ret['dimensions'] = array(
            'default'=>array(
                'text' => trlKwfStatic('default'),
                'width' => 120,
                'height' => 120,
                'cover' => false,
            )
        );
        return $ret;
    }
}
