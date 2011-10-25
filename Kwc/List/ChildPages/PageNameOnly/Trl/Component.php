<?php
class Kwc_List_ChildPages_PageNameOnly_Trl_Component extends Kwc_Chained_Trl_Component
{
    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $page = $this->getData()->getPage();
        $ret['childPages'] = $page->getChildPages();
        return $ret;
    }
}
