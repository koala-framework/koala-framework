<?php
class Vpc_Tabs_Trl_Component extends Vpc_Abstract_List_Trl_Component
{
    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        foreach($ret['listItems'] as $k => $v) {
            $ret['listItems'][$k]['title'] = $v['data']->row->title;
        }
        $ret['extConfig'] = 'Vpc_Abstract_List_Trl_ExtConfigFullSizeEdit';
        return $ret;
    }
}
