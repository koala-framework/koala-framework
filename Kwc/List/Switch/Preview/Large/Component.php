<?php
class Kwc_List_Switch_Preview_Large_Component extends Kwc_Basic_ImageParent_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['dimension'] = array(
            'width' => self::CONTENT_WIDTH,
            'height' => 0,
            'cover' => true,
        );
        $ret['componentName'] = trlKwfStatic('Large Image');
        $ret['imgCssClass'] = 'hideWhileLoading';
        return $ret;
    }
}
