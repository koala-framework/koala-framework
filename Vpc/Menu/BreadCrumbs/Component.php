<?php
class Vpc_Menu_BreadCrumbs_Component extends Vpc_Menu_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['separator'] = '»';
        return $ret;
    }

    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $ret['separator'] = $this->_getSetting('separator');
        $ret['links'] = array();
        $page = $this->getData();
        do {
            $ret['links'][] = $page;
        } while ($page = $page->getParentPage());
        $ret['links'] = array_reverse($ret['links']);
        return $ret;
    }
}
