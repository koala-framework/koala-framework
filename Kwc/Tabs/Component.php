<?php
class Kwc_Tabs_Component extends Kwc_Abstract_List_Component
{
    public static $needsParentComponentClass = true;
    public static function getSettings($parentComponentClass)
    {
        $ret = parent::getSettings();
        $ret['generators']['child']['component'] = $parentComponentClass;
        $ret['componentName'] = trlKwfStatic('Tabs');
        $ret['componentIcon'] = 'tab.png';
        $ret['componentCategory'] = 'layout';
        $ret['componentPriority'] = 80;
        $ret['cssClass'] = 'kwfup-webStandard';
        $ret['assetsDefer']['dep'][] = 'KwfTabs';
        $ret['extConfig'] = 'Kwc_Tabs_ExtConfig';
        $ret['contentWidthSubtract'] = 20;
        return $ret;
    }

    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        foreach($ret['listItems'] as $k => $v) {
            $ret['listItems'][$k]['title'] = $v['data']->row->title;
        }
        return $ret;
    }
}
