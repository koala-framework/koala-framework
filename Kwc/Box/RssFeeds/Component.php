<?php
class Kwc_Box_RssFeeds_Component extends Kwc_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['flags']['hasHeaderIncludeCode'] = true;
        return $ret;
    }

    public function getIncludeCode()
    {
        return $this->getData();
    }

    public function getTemplateVars(Kwf_Component_Renderer_Abstract $renderer = null)
    {
        $ret = parent::getTemplateVars($renderer);
        $feeds = Kwf_Component_Data_Root::getInstance()->getComponentsByClass(
            'Kwc_Abstract_Feed_Component',
            array('subroot' => $this->getData())
        );
        $ret['feeds'] = $feeds;
        return $ret;
    }
}
