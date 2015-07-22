<?php
class Kwc_List_ChildPages_Teaser_TeaserImage_Component extends Kwc_Abstract_Composite_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['child']['component']['text'] =
            'Kwc_List_ChildPages_Teaser_TeaserImage_Text_Component';
        $ret['generators']['child']['component']['image'] =
            'Kwc_List_ChildPages_Teaser_TeaserImage_Image_Component';
        $ret['componentName'] = trlKwfStatic('Teaser image');
        $ret['rootElementClass'] = 'kwfup-webStandard';
        $ret['ownModel'] = 'Kwc_List_ChildPages_Teaser_TeaserImage_Model';
        $ret['throwHasContentChangedOnRowColumnsUpdate'] = 'visible';

        $ret['headlineComponentLinkConfig'] = array();
        $ret['readMoreComponentLinkConfig'] = array();
        return $ret;
    }

    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $ret['readMoreLinktext'] = $this->getRow()->link_text;
        $ret['headlineComponentLinkConfig'] = $this->_getSetting('headlineComponentLinkConfig');
        $ret['readMoreComponentLinkConfig'] = $this->_getSetting('readMoreComponentLinkConfig');
        return $ret;
    }

    public function hasContent()
    {
        if ($this->getRow()->visible) return true;
        return false;
    }
}
