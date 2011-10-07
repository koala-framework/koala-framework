<?php
class Kwc_Basic_ImageEnlarge_TestComponent extends Kwc_Basic_ImageEnlarge_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['child']['component']['linkTag'] = 'Kwc_Basic_ImageEnlarge_EnlargeTag_TestComponent';
        $ret['dimensions'] = array(
            array('width'=>10, 'height'=>10, 'scale'=>Kwf_Media_Image::SCALE_DEFORM)
        );
        $ret['ownModel'] = 'Kwc_Basic_ImageEnlarge_TestModel';
        return $ret;
    }
}
