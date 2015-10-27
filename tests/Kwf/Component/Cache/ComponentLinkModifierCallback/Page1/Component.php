<?php
class Kwf_Component_Cache_ComponentLinkModifierCallback_Page1_Component extends Kwc_Abstract
{

    public function getTemplateVars(Kwf_Component_Renderer_Abstract $renderer = null)
    {
        $ret = parent::getTemplateVars($renderer);
        $ret['linkTarget'] = $this->getData()->parent->getChildComponent('_linkTarget');
        return $ret;
    }

}
