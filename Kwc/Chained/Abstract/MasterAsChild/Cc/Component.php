<?php
class Kwc_Chained_Abstract_MasterAsChild_Cc_Component extends Kwc_Chained_Cc_Component
{
    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $ret['child'] = $this->getData()->getChildComponent('-child');
        return $ret;
    }
}
