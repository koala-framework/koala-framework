<?php
class Kwc_Trl_Simple_Test_Trl_Component extends Kwc_Chained_Trl_Component
{
    public function getTemplateVars(Kwf_Component_Renderer_Abstract $renderer)
    {
        $ret = parent::getTemplateVars($renderer);
        $ret['test2'] = $this->getData()->getChildComponent('_test2');
        return $ret;
    }
}
