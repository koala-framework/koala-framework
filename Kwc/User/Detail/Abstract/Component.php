<?php
abstract class Kwc_User_Detail_Abstract_Component extends Kwc_Abstract_Composite_Component
{
    public function getTemplateVars(Kwf_Component_Renderer_Abstract $renderer)
    {
        $ret = parent::getTemplateVars($renderer);
        $ret['row'] = $this->getData()->parent->row;
        return $ret;
    }
}
