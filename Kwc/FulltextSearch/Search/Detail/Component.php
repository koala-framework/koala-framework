<?php
class Kwc_FulltextSearch_Search_Detail_Component extends Kwc_Directories_Item_Detail_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['viewCache'] = false;
        return $ret;
    }

    public function getTemplateVars(Kwf_Component_Renderer_Abstract $renderer = null)
    {
        $ret = parent::getTemplateVars($renderer);
        $ret['queryParts'] = $this->getData()->parent->getChildComponent('-view')
            ->getComponent()->getSearchForm()->getComponent()->getFormRow()->query;
        return $ret;
    }
}
