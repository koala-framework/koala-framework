<?php
class Vpc_Tabs_Component extends Vpc_Abstract_List_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['child']['component'] = 'Vpc_Paragraphs_Component';
        $ret['componentName'] = trlVps('Tabs');
        $ret['componentIcon'] = new Vps_Asset('tab.png');
        $ret['cssClass'] = 'webStandard';
        $ret['assets']['dep'][] = 'VpsTabs';
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
