<?php
class Vpc_Basic_LinkTag_CommunityVideo_Component extends Vpc_Basic_LinkTag_Abstract_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['componentName'] = trlVps('Link.to CommunityVideo');
        $ret['generators']['video'] = array(
            'class' => 'Vps_Component_Generator_Static',
            'component' => 'Vpc_Advanced_CommunityVideo_Component'
        );
        $ret['assets']['files'][] = 'vps/Vpc/Basic/LinkTag/CommunityVideo/Component.js';
        $ret['assets']['dep'][] = 'VpsComponent';
        $ret['assets']['dep'][] = 'VpsLightbox';
        return $ret;
    }

    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $video = $this->getData()->getChildComponent('-video');
        $ret['video'] = $video;
        $ret['width'] = $video->getComponent()->getRow()->width;
        $ret['height'] = $video->getComponent()->getRow()->height;
        $ret['videoUrl'] = $video->getComponent()->getRow()->url;
        return $ret;
    }
}
