<?php
class Vpc_ListChildPages_PageNameOnly_Component extends Vpc_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['componentName'] = trlVps('List child pages');
        $ret['cssClass'] = 'webStandard';
        return $ret;
    }

    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $page = $this->getData()->getPage();
        $ret['childPages'] = $page->getChildPages();
        return $ret;
    }
}
