<?php
class Vpc_Posts_Write_Success_Component extends Vpc_Formular_Success_Component
{
    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();

        $ret['backUrl'] = $this->getParentComponent();
        while (!($ret['backUrl'] instanceof Vpc_Posts_Component)) {
            $ret['backUrl'] = $ret['backUrl']->getParentComponent();
        }

        $ret['backUrl'] = $ret['backUrl']->getUrl();
        return $ret;
    }
}
