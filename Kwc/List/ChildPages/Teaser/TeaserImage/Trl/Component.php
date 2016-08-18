<?php
class Kwc_List_ChildPages_Teaser_TeaserImage_Trl_Component extends Kwc_Abstract_Composite_Trl_Component
{
    public static function getSettings($masterComponentClass)
    {
        $ret = parent::getSettings($masterComponentClass);
        $ret['ownModel'] = 'Kwc_List_ChildPages_Teaser_TeaserImage_Model';
        return $ret;
    }

    public function getTemplateVars(Kwf_Component_Renderer_Abstract $renderer)
    {
        $ret = parent::getTemplateVars($renderer);
        $ret['readMoreLinktext'] = $this->getRow()->link_text;
        return $ret;
    }

    public function hasContent()
    {
        if ($this->getRow()->visible) return true;
        return false;
    }
}
