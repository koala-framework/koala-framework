<?php
class Kwc_Basic_LinkTag_CommunityVideo_Lightbox_Component extends Kwc_Abstract_Composite_Component
{
    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);
        $ret['generators']['child']['component']['video'] = 'Kwc_Basic_LinkTag_CommunityVideo_Lightbox_Video_Component';
        $ret['contentSender'] = 'Kwc_Basic_LinkTag_CommunityVideo_Lightbox_ContentSender';
        return $ret;
    }

}
