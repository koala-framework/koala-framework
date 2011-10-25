<?php
class Kwc_Basic_Image_EmptyImageComponent extends Kwc_Basic_Image_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['ownModel'] = 'Kwc_Basic_Image_TestModel';
        $ret['emptyImage'] = 'empty.png';
        $ret['dimensions'] = array(array('width'=>16, 'height'=>16, 'scale'=>Kwf_Media_Image::SCALE_DEFORM));
        return $ret;
    }
}
