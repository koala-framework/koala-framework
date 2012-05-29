<?php
class Kwc_List_ChildPages_PageNameOnly_Component extends Kwc_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['componentName'] = trlKwfStatic('List child page names');
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

    public static function getStaticCacheMeta($componentClass)
    {
        $ret = parent::getStaticCacheMeta($componentClass);
        $ret[] = new Kwf_Component_Cache_Meta_Static_Model('Kwf_Component_Model', '{componentId}');
        $ret[] = new Kwf_Component_Cache_Meta_Static_Model('Kwc_Root_Category_GeneratorModel', '{id}');
        return $ret;
    }
}
