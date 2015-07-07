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

    protected function _getSearchDirectory()
    {
        $ret = Kwf_Component_Data_Root::getInstance()
            ->getComponentByClass('Kwc_FulltextSearch_Search_Directory_Trl_Component',
                array('subroot'=>$this->getData()));
        if ($ret) {
            $ret = $ret->getChildComponent('-child');
        }
        return $ret;
    }

    /**
     * for Cc
     */
    public final function getSearchDirectory()
    {
        return $this->_getSearchDirectory();
    }

    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $searchPage = $this->_getSearchDirectory();
        $ret['searchForm'] = null;
        if ($searchPage) {
            $ret['searchForm'] =$searchPage->getChildComponent('-view')
                                           ->getChildComponent('-searchForm');
            $ret['config']['searchTitle'] = $searchPage->getTitle();
            $ret['config']['searchUrl'] = $searchPage->url;
        }
        return $ret;
    }
}