<?php
class Vpc_Columns_Trl_Component extends Vpc_Abstract_List_Trl_Component
{
    public static function getSettings($masterComponentClass)
    {
        $ret = parent::getSettings($masterComponentClass);
        $ret['assetsAdmin']['files'][] = 'vps/Vpc/Columns/Trl/Panel.js';
        $ret['assetsAdmin']['dep'][] = 'VpsAutoGrid';
        $ret['assetsAdmin']['dep'][] = 'VpsComponent';
        return $ret;
    }

    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        foreach($ret['listItems'] as $k => $v) {
            $ret['listItems'][$k]['width'] = $v['data']->chained->row->width;
        }
        return $ret;
    }
}
