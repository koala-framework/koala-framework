<?php
class Kwc_Basic_LinkTag_Youtube_Video_Component extends Kwc_Advanced_Youtube_Component
{
    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);
        $ret['assetsDefer']['dep'][] = 'KwfLightbox';
        $ret['contentSender'] = 'Kwc_Basic_LinkTag_Youtube_Video_ContentSender';
        $ret['playerVars']['showinfo'] = 0;
        return $ret;
    }
}
