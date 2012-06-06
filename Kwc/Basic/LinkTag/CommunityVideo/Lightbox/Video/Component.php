<?php
class Kwc_Basic_LinkTag_CommunityVideo_Lightbox_Video_Component extends Kwc_Advanced_CommunityVideo_Component
{
    protected function _getFlashData()
    {
        $ret = parent::_getFlashData();
        $ret['url'] .= '&autoplay=1';
        return $ret;
    }
}
