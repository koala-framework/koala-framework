<?php
class Kwc_FulltextSearch_Box_Component extends Kwc_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['useLiveSearch'] = true;
        $ret['hideSubmit'] = false;
        $ret['flags']['forwardProcessInput'] = true;
        $ret['minSearchTermLength'] = 3;
        $ret['flags']['noIndex'] = true;
        return $ret;
    }

    public function getForwardProcessInputComponents()
    {
        return array(
            $this->_getSearchDirectory()->getChildComponent('-view')->getChildComponent('-searchForm')
        );
    }

    protected function _getSearchDirectory()
    {
        return Kwf_Component_Data_Root::getInstance()
                ->getComponentByClass('Kwc_FulltextSearch_Search_Directory_Component',
                                    array('subroot'=>$this->getData()));
    }

    /**
    * for Cc
    */
    public final function getSearchDirectory()
    {
        return $this->_getSearchDirectory();
    }

    public function getTemplateVars(Kwf_Component_Renderer_Abstract $renderer = null)
    {
        $ret = parent::getTemplateVars($renderer);
        $searchPage = $this->_getSearchDirectory();
        $ret['searchForm'] = $searchPage->getChildComponent('-view')->getChildComponent('-searchForm');
        $ret['config'] = array(
            'searchTitle' => $searchPage->getTitle(),
            'searchUrl' => $searchPage->url,
            'useLiveSearch' => $this->_getSetting('useLiveSearch'),
            'hideSubmit' => $this->_getSetting('hideSubmit'),
            'minSearchTermLength' => $this->_getSetting('minSearchTermLength')
        );
        return $ret;
    }
}
