<?php
class Vpc_Columns_Trl_Component extends Vpc_Chained_Trl_Component
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
        $s = new Vps_Component_Select();
        $s->whereGenerator('columns');
        $s->order('pos');
        $ret['columns'] = $this->getData()->getChildComponents($s);
        return $ret;
    }

    public function hasContent()
    {
        foreach ($this->getData()->getChildComponents(array('generator' => 'columns')) as $c) {
            if ($c->getComponent()->hasContent()) return true;
        }
        return false;
    }
}
