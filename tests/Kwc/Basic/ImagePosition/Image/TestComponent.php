<?php
class Kwc_Basic_ImagePosition_Image_TestComponent extends Kwc_Basic_Image_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['ownModel'] = 'Kwc_Basic_ImagePosition_Image_TestModel';
        $ret['dimensions'] = array(array('width'=>100, 'height'=>100, 'scale'=>Kwf_Media_Image::SCALE_BESTFIT));
        return $ret;
    }
}
