<?php
class Kwc_Tabs_Trl_Component extends Kwc_Abstract_List_Trl_Component
{
    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        foreach($ret['listItems'] as $k => $v) {
            $ret['listItems'][$k]['title'] = $v['data']->generator->getTrlRowByData($v['data'])->title;
        }
        return $ret;
    }
}
