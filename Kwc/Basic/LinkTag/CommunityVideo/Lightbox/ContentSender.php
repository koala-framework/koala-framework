<?php
class Kwc_Basic_LinkTag_CommunityVideo_Lightbox_ContentSender extends Kwf_Component_Abstract_ContentSender_Lightbox
{
    protected function _getOptions()
    {
        $ret = parent::_getOptions();
        $ret['width'] = $this->_data->getChildComponent('-video')->getComponent()->getRow()->width;
        $ret['height'] = $this->_data->getChildComponent('-video')->getComponent()->getRow()->height;
        return $ret;
    }
}
