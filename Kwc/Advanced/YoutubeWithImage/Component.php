<?php
class Kwc_Advanced_YoutubeWithImage_Component extends Kwc_Abstract_Composite_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['componentName'] = trlKwfStatic('Youtube with Teaser Image');
        $ret['generators']['child']['component']['youtube'] = 'Kwc_Advanced_YoutubeWithImage_Youtube_Component';
        $ret['generators']['child']['component']['image'] = 'Kwc_Advanced_YoutubeWithImage_Image_Component';
        return $ret;
    }
}
