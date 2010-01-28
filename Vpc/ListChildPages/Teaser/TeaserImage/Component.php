<?php
class Vpc_ListChildPages_Teaser_TeaserImage_Component extends Vpc_Abstract_Composite_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['child']['component']['text'] =
            'Vpc_ListChildPages_Teaser_TeaserImage_Text_Component';
        $ret['generators']['child']['component']['image'] =
            'Vpc_ListChildPages_Teaser_TeaserImage_Image_Component';
        $ret['componentName'] = trlVps('Teaser image');
        $ret['cssClass'] = 'webStandard';
        $ret['ownModel'] = 'Vps_Component_FieldModel';
        return $ret;
    }

    public function hasContent()
    {
        if ($this->getRow()->visible) return true;
        return false;
    }
}
