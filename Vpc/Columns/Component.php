<?php
class Vpc_Columns_Component extends Vpc_Abstract_List_Component
{
    public static function getSettings($parentComponentClass)
    {
        $ret = parent::getSettings();
        $ret['needsParentComponentClass'] = true;
        $ret['generators']['child']['component'] = $parentComponentClass;
        $ret['componentName'] = trlVps('Columns');
        $ret['componentIcon'] = new Vps_Asset('application_tile_horizontal');

        $ret['extConfig'] = 'Vpc_Abstract_List_ExtConfigEditButton';
        return $ret;
    }

    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        foreach($ret['listItems'] as $k => $v) {
            $w = $v['data']->row->width;
            if (is_numeric($w)) $w .= 'px'; //standard-einheit
            $ret['listItems'][$k]['width'] = $w;
        }
        return $ret;
    }
}
