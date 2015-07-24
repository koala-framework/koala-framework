<?php
class Kwf_Component_Cache_FullPage_Test2_Component extends Kwc_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['ownModel'] = 'Kwf_Component_Cache_FullPage_Test2_Model';
        return $ret;
    }

    public function getTemplateVars(Kwf_Component_Renderer_Abstract $renderer = null)
    {
        $ret = parent::getTemplateVars($renderer);
        $ret['test4'] = $this->getData()->parent->getChildComponent('_test4');
        return $ret;
    }
}
