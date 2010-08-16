<?php
class Vpc_List_ChildPages_PageNameOnly_Component extends Vpc_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['componentName'] = trlVps('List child page names');
        $ret['cssClass'] = 'webStandard';
        return $ret;
    }

    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $page = $this->getData()->getPage();
        $ret['childPages'] = $page->getChildPages();
        return $ret;
    }

    public static function getStaticCacheMeta()
    {
        $ret = parent::getStaticCacheMeta();
        $ret[] = new Vps_Component_Cache_Meta_Static_Model('Vps_Component_Model', '{componentId}');
        $ret[] = new Vps_Component_Cache_Meta_Static_Model('Vpc_Root_Category_GeneratorModel', '{id}');
        return $ret;
    }
}
