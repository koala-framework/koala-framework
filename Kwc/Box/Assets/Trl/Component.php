<?php
class Kwc_Box_Assets_Trl_Component extends Kwc_Box_Assets_Component
{
    public function getTemplateVars(Kwf_Component_Renderer_Abstract $renderer = null)
    {
        $ret = parent::getTemplateVars($renderer);
        $ret['language'] = $this->getData()->getLanguage();
        return $ret;
    }
}
