<?php
class Kwc_Basic_LinkTag_Youtube_Video_Component extends Kwc_Advanced_Youtube_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['assets']['dep'][] = 'KwfLightbox';
        $ret['contentSender'] = 'Kwc_Basic_LinkTag_Youtube_Video_ContentSender';
        $ret['playerVars']['showinfo'] = 0;
        return $ret;
    }
}
