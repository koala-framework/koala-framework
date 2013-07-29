<?php
class Kwc_FulltextSearch_Box_Component extends Kwc_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['assets']['files'][] = 'kwf/Kwc/FulltextSearch/Box/Component.js';
        $ret['assets']['dep'][] = 'KwfOnReady';
        $ret['useLiveSearch'] = true;
        $ret['hideSubmit'] = false;
        $ret['flags']['forwardProcessInput'] = true;
        return $ret;
    }

    public function getForwardProcessInputComponents()
    {
        return array(
            Kwf_Component_Data_Root::getInstance()
                ->getComponentByClass('Kwc_FulltextSearch_Search_Directory_Component',
                                    array('subroot'=>$this->getData()))
                ->getChildComponent('-view')->getChildComponent('-searchForm')
            );
    }


    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $searchPage = Kwf_Component_Data_Root::getInstance()
            ->getComponentByClass('Kwc_FulltextSearch_Search_Directory_Component',
                                   array('subroot'=>$this->getData()));
        $ret['searchForm'] = $searchPage->getChildComponent('-view')->getChildComponent('-searchForm');
        $ret['config'] = array(
            'searchTitle' => $searchPage->getTitle(),
            'searchUrl' => $searchPage->getAbsoluteUrl(),
            'useLiveSearch' => $this->_getSetting('useLiveSearch'),
            'hideSubmit' => $this->_getSetting('hideSubmit'),
        );
        return $ret;
    }
}
