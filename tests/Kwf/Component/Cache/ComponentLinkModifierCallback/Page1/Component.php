<?php
class Kwf_Component_Cache_ComponentLinkModifierCallback_Page1_Component extends Kwc_Abstract
{

    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $ret['linkTarget'] = $this->getData()->parent->getChildComponent('_linkTarget');
        return $ret;
    }

}
