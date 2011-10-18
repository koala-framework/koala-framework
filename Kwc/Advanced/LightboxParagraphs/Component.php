<?php
class Kwc_Advanced_LightboxParagraphs_Component extends Vpc_Paragraphs_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['componentName'] = trlVps('Paragraphs in Lightbox');
        $ret['contentSender'] = 'Kwf_Component_Abstract_ContentSender_Lightbox';
        return $ret;
    }
}