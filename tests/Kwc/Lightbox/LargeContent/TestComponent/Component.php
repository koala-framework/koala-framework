<?php
class Kwc_Lightbox_LargeContent_TestComponent_Component extends Kwc_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['contentSender'] = 'Kwf_Component_Abstract_ContentSender_Lightbox';
        $ret['assets']['dep'][] = 'KwfLightbox';
        return $ret;
    }
}
