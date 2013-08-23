<?php
class RedMallee_List_Teaser_Item_Image_Component extends Kwc_Basic_Image_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['dimensions'] = array(
            array(
                'text' => trlKwfStatic('user-defined'),
                'width' => '285',
                'height' => '160',
                'scale' => Kwf_Media_Image::SCALE_CROP
            )
        );
        return $ret;
    }

}
