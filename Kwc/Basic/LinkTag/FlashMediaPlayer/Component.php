<?php
class Kwc_Basic_LinkTag_FlashMediaPlayer_Component extends Kwc_Basic_LinkTag_CommunityVideo_Component
{
    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);
        $ret['componentName'] = trlKwfStatic('Link.to FlashMediaPlayer');
        $ret['generators']['video']['component'] = 'Kwc_Basic_LinkTag_FlashMediaPlayer_Lightbox_Component';
        return $ret;
    }
}
