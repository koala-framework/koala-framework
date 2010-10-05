<?php
class Vpc_Chained_Trl_MasterAsChild_Cc_Component extends Vpc_Chained_Cc_Component
{
    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $ret['child'] = $this->getData()->getChildComponent('-child');
        return $ret;
    }
}
