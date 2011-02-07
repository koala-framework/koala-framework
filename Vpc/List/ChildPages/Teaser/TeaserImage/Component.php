<?php
class Vpc_List_ChildPages_Teaser_TeaserImage_Component extends Vpc_Abstract_Composite_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['child']['component']['text'] =
            'Vpc_List_ChildPages_Teaser_TeaserImage_Text_Component';
        $ret['generators']['child']['component']['image'] =
            'Vpc_List_ChildPages_Teaser_TeaserImage_Image_Component';
        $ret['componentName'] = trlVps('Teaser image');
        $ret['cssClass'] = 'webStandard';
        $ret['ownModel'] = 'Vpc_List_ChildPages_Teaser_TeaserImage_Model';
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

    public static function getStaticCacheVars($componentClass)
    {
        $ret = parent::getStaticCacheVars($componentClass);
        $ret[] = array(
            'model' => 'Vpc_Root_Category_GeneratorModel'
        );
        return $ret;
    }
}
