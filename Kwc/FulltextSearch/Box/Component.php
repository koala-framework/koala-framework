<?php
class Kwc_FulltextSearch_Box_Component extends Kwc_Abstract
{
    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);
        $ret['useLiveSearch'] = true;
        $ret['hideSubmit'] = false;
        $ret['flags']['forwardProcessInput'] = true;
        $ret['minSearchTermLength'] = 3;
        return $ret;
    }

    public function getForwardProcessInputComponents()
    {
        return array(
            $this->_getSearchDirectory()->getChildComponent('-searchForm')
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

    public function getTemplateVars(Kwf_Component_Renderer_Abstract $renderer)
    {
        $ret = parent::getTemplateVars($renderer);
        $searchPage = $this->_getSearchDirectory();
        $ret['searchForm'] = $searchPage->getChildComponent('-searchForm');
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
