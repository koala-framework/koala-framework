<?php
class Kwc_Tabs_Component extends Kwc_Abstract_List_Component
{
    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);
        $ret['generators']['child']['component'] = 'Kwc_Paragraphs_Component';
        $ret['componentName'] = trlKwfStatic('Tabs');
        $ret['componentIcon'] = 'tab.png';
        $ret['componentCategory'] = 'layout';
        $ret['componentPriority'] = 80;
        $ret['rootElementClass'] = 'kwfUp-webStandard';
        $ret['assetsDefer']['dep'][] = 'KwfTabs';
        $ret['extConfig'] = 'Kwc_Tabs_ExtConfig';
        $ret['contentWidthSubtract'] = 20;
        return $ret;
    }

    public function getTemplateVars(Kwf_Component_Renderer_Abstract $renderer)
    {
        $ret = parent::getTemplateVars($renderer);
        foreach($ret['listItems'] as $k => $v) {
            $ret['listItems'][$k]['title'] = $v['data']->row->title;
        }
        return $ret;
    }
}
