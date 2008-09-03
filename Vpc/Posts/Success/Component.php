<?php
class Vpc_Posts_Success_Component extends Vpc_Form_Success_Component
{
    protected function _getTargetPage()
    {
        return $this->getData()->getParentPage();
    }
    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $ret['targetPage'] = $this->_getTargetPage();
        return $ret;
    }

}
