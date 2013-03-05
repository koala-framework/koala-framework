<?php
class Kwc_Shop_Box_Cart_Trl_Component extends Kwc_Chained_Trl_Component
{
    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        foreach ($ret['links'] as $k=>$i) {
            $ret['links'][$k]['component'] = Kwc_Chained_Trl_Component::getChainedByMaster($i['component'], $this->getData());
        }
        return $ret;
    }
}
