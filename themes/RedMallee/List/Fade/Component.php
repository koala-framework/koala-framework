<?php
class RedMallee_List_Fade_Component extends Kwc_List_Fade_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['child']['component'] = 'RedMallee_List_Fade_Image_Component';
        $ret['componentName'] = trlStatic('Stage');
        $ret['fadeConfig']['easingFadeOut'] = 'easeInQuad';
        $ret['fadeConfig']['easingFadeIn'] = 'easeOutQuad';
        return $ret;
    }
}
