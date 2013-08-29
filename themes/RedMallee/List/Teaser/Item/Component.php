<?php
class RedMallee_List_Teaser_Item_Component extends Kwc_Abstract_Composite_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['componentName'] = trlStatic('Item');
        $ret['generators']['child']['component']['headline'] = 'Kwc_Basic_Headline_Component';
        $ret['generators']['child']['component']['image'] = 'RedMallee_List_Teaser_Item_Image_Component';
        $ret['generators']['child']['component']['text'] = 'Kwc_Basic_Text_Component';
        $ret['generators']['child']['component']['linkTag'] = 'Kwc_Basic_LinkTag_Component';
        return $ret;
    }
}
