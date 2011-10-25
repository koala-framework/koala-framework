<?php
class Kwc_Advanced_LightboxParagraphs_Component extends Kwc_Paragraphs_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['componentName'] = trlKwf('Paragraphs in Lightbox');
        $ret['contentSender'] = 'Kwf_Component_Abstract_ContentSender_Lightbox';
        return $ret;
    }
}