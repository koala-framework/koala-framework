<?php
class RedMallee_List_BottomStage_Component extends Kwc_Abstract_List_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['componentName'] = trlStatic('Stage unten');
        $ret['cssClass'] = ' webStandard';
        $ret['generators']['child']['component'] = 'RedMallee_List_BottomStage_Item_Component';
        return $ret;
    }
}
