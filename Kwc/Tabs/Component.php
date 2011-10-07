<?php
class Kwc_Tabs_Component extends Kwc_Abstract_List_Component
{
    public static function getSettings($parentComponentClass)
    {
        $ret = parent::getSettings();
        $ret['needsParentComponentClass'] = true;
        $ret['generators']['child']['component'] = $parentComponentClass;
        $ret['componentName'] = trlKwf('Tabs');
        $ret['componentIcon'] = new Kwf_Asset('tab.png');
        $ret['cssClass'] = 'webStandard';
        $ret['assets']['dep'][] = 'KwfTabs';
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
