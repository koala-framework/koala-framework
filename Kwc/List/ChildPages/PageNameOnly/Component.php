<?php
class Kwc_List_ChildPages_PageNameOnly_Component extends Kwc_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['componentName'] = trlKwfStatic('List child page names');
        $ret['componentCategory'] = 'childPages';
        $ret['rootElementClass'] = 'kwfUp-webStandard';
        return $ret;
    }

    public function getTemplateVars(Kwf_Component_Renderer_Abstract $renderer)
    {
        $ret = parent::getTemplateVars($renderer);
        $page = $this->getData()->getPage();
        $ret['childPages'] = $page->getChildPages();
        return $ret;
    }
}
