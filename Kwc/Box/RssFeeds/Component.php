<?php
class Vpc_Box_RssFeeds_Component extends Vpc_Abstract
{
    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $feeds = Vps_Component_Data_Root::getInstance()->getComponentsByClass(
            'Vpc_Abstract_Feed_Component',
            array('subroot' => $this->getData())
        );
        $ret['feeds'] = $feeds;
        return $ret;
    }
}
