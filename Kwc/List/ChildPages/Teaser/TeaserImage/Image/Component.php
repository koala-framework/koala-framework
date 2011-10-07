<?php
class Vpc_List_ChildPages_Teaser_TeaserImage_Image_Component extends Vpc_Basic_Image_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['dimensions']['default'] = array(
            'text' => trlVps('default'),
            'width' => 100,
            'height' => 75,
            'scale' => Vps_Media_Image::SCALE_BESTFIT
        );
        return $ret;
    }
}
