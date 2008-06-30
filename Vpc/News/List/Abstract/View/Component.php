<?php
class Vpc_News_List_Abstract_View_Component extends Vpc_Abstract
{
    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();

        $parent = $this->getData()->parent->getComponent();
        $ret['news'] = $parent->getNews();
        return $ret;
    }

    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['cssClass'] = 'webStandard';
        return $ret;
    }
}
