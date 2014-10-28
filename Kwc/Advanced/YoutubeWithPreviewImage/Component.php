<?php
class Kwc_Advanced_YoutubeWithPreviewImage_Component extends Kwc_Advanced_Youtube_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['cssClass'] = 'webStandard';
        $ret['componentName'] = trlKwfStatic('Youtube with Teaser Image');
        $ret['generators']['child']['component']['previewImage'] = 'Kwc_Advanced_YoutubeWithPreviewImage_Image_Component';
        $ret['playerVars']['showinfo'] = 0;
        return $ret;
    }

    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $ret['image'] = $this->getData()->getChildComponent('-image');
        return $ret;
    }
}
