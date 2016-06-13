<?php
class Kwc_Advanced_LightboxParagraphs_Component extends Kwc_Paragraphs_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['componentName'] = trlKwfStatic('Paragraphs in Lightbox');
        $ret['contentSender'] = 'Kwf_Component_Abstract_ContentSender_Lightbox';
        $ret['assetsDefer']['dep'][] = 'KwfLightbox';

        $ret['contentWidth'] = 600;
        return $ret;
    }
}
