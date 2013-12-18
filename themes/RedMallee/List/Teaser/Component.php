<?php
class RedMallee_List_Teaser_Component extends Kwc_Abstract_List_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['componentName'] = trlStatic('Teaser Liste');
        $ret['cssClass'] = ' webStandard';
        $ret['generators']['child']['component'] = 'RedMallee_List_Teaser_Item_Component';
        return $ret;
    }
}
