<?php
class Vpc_Trl_Simple_Test_Trl_Component extends Vpc_Chained_Trl_Component
{
    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $ret['test2'] = $this->getData()->getChildComponent('_test2');
        return $ret;
    }
}
