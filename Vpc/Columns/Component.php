<?php
class Vpc_Columns_Component extends Vpc_Abstract_List_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['child']['component'] = 'Vpc_Paragraphs_Component';
        $ret['componentName'] = trlVps('Columns');
        $ret['componentIcon'] = new Vps_Asset('application_tile_horizontal');
        return $ret;
    }

    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        foreach($ret['listItems'] as $k => $v) {
            $ret['listItems'][$k]['width'] = $v['data']->row->width;
        }
        return $ret;
    }
}
