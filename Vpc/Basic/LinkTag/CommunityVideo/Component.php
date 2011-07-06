<?php
class Vpc_Basic_LinkTag_CommunityVideo_Component extends Vpc_Basic_LinkTag_Lightbox_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['componentName'] = trlVps('Link.to CommunityVideo');
        $ret['generators']['video']['component'] = 'Vpc_Advanced_CommunityVideo_Component';
        return $ret;
    }

    protected function _getPopupVars($child)
    {
        return array(
            'width' => $child->getComponent()->getRow()->width,
            'height' => $child->getComponent()->getRow()->height,
            'url' => $child->getComponent()->getRow()->url
        );
    }
}
