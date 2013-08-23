<?php
class RedMallee_Box_Logo_Component extends Kwc_Basic_Image_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['componentName'] = trlKwfStatic('Logo');
        $ret['assets']['files'][] = 'kwf/themes/RedMallee/Box/Logo/Component.js';
        $ret['dimensions'] = array(
            'customcrop'=>array(
                'text' => trlKwf('user-defined'),
                'width' => 305,
                'height' => 100,
                'scale' => Kwf_Media_Image::SCALE_CROP
            )
        );
        return $ret;
    }
}
