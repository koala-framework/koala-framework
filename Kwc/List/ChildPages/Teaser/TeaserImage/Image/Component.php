<?php
class Kwc_List_ChildPages_Teaser_TeaserImage_Image_Component extends Kwc_Basic_Image_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['dimensions']['default'] = array(
            'text' => trlKwf('default'),
            'width' => 100,
            'height' => 75,
            'scale' => Kwf_Media_Image::SCALE_BESTFIT
        );
        return $ret;
    }
}
