<?php
class Kwc_Trl_ImageEnlarge_ImageEnlarge_EnlargeTag_Component extends Kwc_Basic_ImageEnlarge_EnlargeTag_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['ownModel'] = 'Kwc_Trl_ImageEnlarge_ImageEnlarge_EnlargeTag_TestModel';
        $ret['dimensions'] = array(array(
            'width'=>null, 'height'=>null, 'scale'=>Kwf_Media_Image::SCALE_ORIGINAL
        ));
        $ret['imageTitle'] = false;
        return $ret;
    }
}
