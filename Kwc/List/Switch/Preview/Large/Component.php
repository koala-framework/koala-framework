<?php
class Kwc_List_Switch_Preview_Large_Component extends Kwc_Basic_Image_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['dimensions'] = array(
        'default'=>array(
            'width' => self::CONTENT_WIDTH,
            'height' => 0,
            'scale' => Kwf_Media_Image::SCALE_CROP
        ));
        $ret['componentName'] = trlKwfStatic('Large Image');
        $ret['useParentImage'] = true;
        $ret['imgCssClass'] = 'hideWhileLoading';
        return $ret;
    }
}
