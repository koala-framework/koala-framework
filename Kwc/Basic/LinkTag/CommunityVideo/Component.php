<?php
class Kwc_Basic_LinkTag_CommunityVideo_Component extends Kwc_Basic_LinkTag_Abstract_Component
{
    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);
        $ret['componentName'] = trlKwfStatic('Link.to CommunityVideo');
        $ret['generators']['video'] = array(
            'class' => 'Kwf_Component_Generator_Page_Static',
            'component' => 'Kwc_Basic_LinkTag_CommunityVideo_Lightbox_Component',
            'name' => trlKwfStatic('Video'),
        );
        $ret['dataClass'] = 'Kwc_Basic_LinkTag_CommunityVideo_Data';
        return $ret;
    }

    public function hasContent()
    {
        return $this->getData()->getChildComponent('_video')->getComponent()->hasContent();
    }
}
