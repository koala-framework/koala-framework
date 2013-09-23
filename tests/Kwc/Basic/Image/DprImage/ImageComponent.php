<?php
class Kwc_Basic_Image_DprImage_ImageComponent extends Kwc_Basic_Image_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['ownModel'] = 'Kwc_Basic_Image_DprImage_TestModel';
        $ret['dimensions'] = array(
            array(
                'width' => 32,
                'height' => 32,
                'bestfit' => true,
            )
        );
        return $ret;
    }
}
