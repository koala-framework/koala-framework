<?php
class Kwc_List_ChildPages_Teaser_TeaserImage_Image_Component extends Kwc_Basic_Image_Component
{
    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);
        $ret['dimensions']['default'] = array(
            'text' => trlKwfStatic('default'),
            'width' => 100,
            'height' => 75,
            'cover' => false,
        );
        $ret['defineWidth'] = true;
        return $ret;
    }
}
