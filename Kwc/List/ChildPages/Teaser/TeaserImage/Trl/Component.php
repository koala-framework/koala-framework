<?php
class Kwc_List_ChildPages_Teaser_TeaserImage_Trl_Component extends Kwc_Abstract_Composite_Trl_Component
{
    public static function getSettings($masterComponentClass)
    {
        $ret = parent::getSettings($masterComponentClass);
        $ret['ownModel'] = 'Kwc_List_ChildPages_Teaser_TeaserImage_Model';
        return $ret;
    }

    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $ret['readMoreLinktext'] = $this->getRow()->link_text;
        return $ret;
    }

    public function hasContent()
    {
        if ($this->getRow()->visible) return true;
        return false;
    }

    public static function getStaticCacheMeta($componentClass)
    {
        $ret = parent::getStaticCacheMeta($componentClass);
        $ret[] = new Kwf_Component_Cache_Meta_Static_Model('Kwc_Root_Category_Trl_GeneratorModel');
        return $ret;
    }
}
