<?php
class Kwc_Basic_Html_Trl_Component extends Kwc_Abstract_Composite_Trl_Component
{
    public static function getSettings($childComponentClass)
    {
        $ret = parent::getSettings($childComponentClass);
        $ret['ownModel'] = 'Kwc_Basic_Html_Model';
        $ret['throwHasContentChangedOnRowColumnsUpdate'] = 'content';
        $ret['assetsAdmin']['files'][] = 'kwf/Kwc/Basic/Html/Trl/CopyButton.js';
        return $ret;
    }

    public function getTemplateVars(Kwf_Component_Renderer_Abstract $renderer)
    {
        $ret = parent::getTemplateVars($renderer);
        $ret['content'] = $this->getRow()->content;
        return $ret;
    }

    public function hasContent()
    {
        return trim($this->getRow()->content) != '';
    }

    public function getExportData()
    {
        $ret = parent::getExportData();
        $ret['content'] = $this->getRow()->content;
        return $ret;
    }
}
