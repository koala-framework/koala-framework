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

    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();

        $parent = $this->getData()->parent->getComponent();
        $ret['news'] = $parent->getNews();
        return $ret;
    }
}
