<?php
class Kwc_Basic_Link_Trl_Component extends Kwc_Abstract_Composite_Trl_Component
{
    public static function getSettings($mainComponentClass)
    {
        $ret = parent::getSettings($mainComponentClass);
        $ret['ownModel'] = Kwc_Abstract::getSetting($mainComponentClass, 'ownModel');
        $ret['assetsAdmin']['dep'][] = 'ExtFormFields';
        $ret['assetsAdmin']['files'][] = 'kwf/Kwc/Basic/Link/Trl/CopyButton.js';
        $ret['throwHasContentChangedOnRowColumnsUpdate'] = 'text';
        return $ret;
    }

    public function getTemplateVars(Kwf_Component_Renderer_Abstract $renderer)
    {
        $ret = parent::getTemplateVars($renderer);
        $ret['text'] = $this->_getRow()->text;
        return $ret;
    }

    public function hasContent()
    {
        if (!$this->_getRow()->text) return false;
        return parent::hasContent();
    }
}
