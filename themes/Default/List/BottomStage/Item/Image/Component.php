<?php
class Default_List_BottomStage_Item_Image_Component extends Kwc_Basic_Image_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['dimensions'] = array(
            array(
                'text' => trlKwf('user-defined'),
                'width' => '255',
                'height' => '147',
                'scale' => Kwf_Media_Image::SCALE_CROP
            )
        );
        return $ret;
    }

}
