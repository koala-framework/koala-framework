<?php
class Vpc_Tabs_Component extends Vpc_Abstract_List_Component
{
    public static function getSettings($parentComponentClass)
    {
        $ret = parent::getSettings();
        $ret['needsParentComponentClass'] = true;
        $ret['generators']['child']['component'] = $parentComponentClass;
        $ret['componentName'] = trlVps('Tabs');
        $ret['componentIcon'] = new Vps_Asset('tab.png');
        $ret['cssClass'] = 'webStandard';
        $ret['assets']['dep'][] = 'VpsTabs';
        $ret['extConfig'] = 'Vpc_Tabs_ExtConfig';
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
