<?php
class Kwc_FulltextSearch_Box_Trl_Component extends Kwc_Chained_Trl_Component
{
    public function processInput($postData)
    {
        $searchPage = Kwf_Component_Data_Root::getInstance()
            ->getComponentByClass('Kwc_FulltextSearch_Search_Directory_Trl_Component',
                                   array('subroot'=>$this->getData()));
        if ($searchPage) {
            $searchPage
                ->getChildComponent('-child')
                ->getChildComponent('-view')->getChildComponent('-searchForm')
                ->getComponent()->processInput($postData);
        }
    }

    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $searchPage = Kwf_Component_Data_Root::getInstance()
            ->getComponentByClass('Kwc_FulltextSearch_Search_Directory_Trl_Component',
                                   array('subroot'=>$this->getData()));
        if ($searchPage) {
            $searchPage = $searchPage->getChildComponent('-child');
        }
        $ret['searchForm'] = null;
        if ($searchPage) {
            $ret['searchForm'] =$searchPage->getChildComponent('-view')
                                           ->getChildComponent('-searchForm');
            $ret['config']['searchTitle'] = $searchPage->getTitle();
            $ret['config']['searchUrl'] = $searchPage->getAbsoluteUrl();
        }
        return $ret;
    }
}