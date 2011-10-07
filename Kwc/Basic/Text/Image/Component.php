<?php
class Kwc_Basic_Text_Image_Component extends Kwc_Basic_Image_Component
{
    public static function getSettings()
    {
        return array_merge(parent::getSettings(),
            array('allowBlank' => false,
                  'dimension'  => array(),
                  'scale'      => array(Kwf_Media_Image::SCALE_DEFORM),
            ));
    }
}
