<?php
class Kwc_Advanced_YoutubeWithPreviewImage_Component extends Kwc_Advanced_Youtube_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['rootElementClass'] = 'kwfUp-webStandard';
        $ret['componentName'] = trlKwfStatic('Youtube with Teaser Image');
        $ret['generators']['child']['component']['previewImage'] = 'Kwc_Advanced_YoutubeWithPreviewImage_Image_Component';
        return $ret;
    }


    public function getTemplateVars(Kwf_Component_Renderer_Abstract $renderer)
    {
        $ret = parent::getTemplateVars($renderer);
        //always enable autoplay to play after clicking preview image
        $ret['config']['playerVars']['autoplay'] = 1;
        return $ret;
    }

}
