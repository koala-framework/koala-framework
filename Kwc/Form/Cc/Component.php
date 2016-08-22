<?php
class Kwc_Form_Cc_Component extends Kwc_Chained_Cc_Component
{
    public function getTemplateVars(Kwf_Component_Renderer_Abstract $renderer)
    {
        $ret = parent::getTemplateVars($renderer);
        $ret['action'] = $this->getData()->url;
        return $ret;
    }
}
