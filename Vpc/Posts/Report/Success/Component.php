<?php
class Vpc_Posts_Report_Success_Component extends Vpc_Formular_Success_Component
{
    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $ret['backUrl'] = $this->getParentComponent()->getParentComponent()->getUrl();
        return $ret;
    }
}
