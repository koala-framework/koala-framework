<?php
class Kwc_Posts_Success_Component extends Kwc_Form_Success_Component
{
    protected function _getTargetPage()
    {
        return $this->getData()->getPage();
    }
    public function getTemplateVars(Kwf_Component_Renderer_Abstract $renderer)
    {
        $ret = parent::getTemplateVars($renderer);
        $ret['targetPage'] = $this->_getTargetPage();
        return $ret;
    }

}
