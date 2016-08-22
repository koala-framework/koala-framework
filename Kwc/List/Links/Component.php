<?php
class Kwc_List_Links_Component extends Kwc_Abstract_List_Component
{
    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);
        $ret['generators']['child']['component'] = 'Kwc_Basic_Link_Component';
        $ret['componentName'] = trlKwfStatic('Links');
        $ret['componentIcon'] = 'links';
        $ret['componentCategory'] = 'content';
        $ret['componentPriority'] = 30;
        $ret['rootElementClass'] = 'kwfUp-webStandard';
        return $ret;
    }
}
