<?php
class Kwc_Basic_LinkTag_FlashMediaPlayer_Lightbox_Component extends Kwc_Basic_LinkTag_CommunityVideo_Lightbox_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['child']['component']['video'] = 'Kwc_Basic_FlashMediaPlayer_Component';
        return $ret;
    }

}
