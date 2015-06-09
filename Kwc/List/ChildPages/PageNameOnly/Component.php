<?php
class Kwc_List_ChildPages_PageNameOnly_Component extends Kwc_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['componentName'] = trlKwfStatic('List child page names');
        $ret['componentCategory'] = 'childPages';
        $ret['cssClass'] = 'kwfup-webStandard';
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
