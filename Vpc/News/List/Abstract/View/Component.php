<?php
class Vpc_News_List_Abstract_View_Component extends Vpc_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['cssClass'] = 'webStandard';
        $ret['viewCache'] = false;
        return $ret;
    }
    
    protected function _getNews()
    {
        return $this->getData()->parent->getComponent()->getNews();
    }

    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $ret['news'] = $this->_getNews();
        return $ret;
    }
}
