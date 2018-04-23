<?php
class Kwc_Statistics_OptBox_Trl_Component extends Kwc_Abstract_Composite_Trl_Component
{
    public static function getSettings($childComponentClass = null)
    {
        $ret = parent::getSettings($childComponentClass);
        $ret['ownModel'] = 'Kwf_Component_FieldModel';
        $ret['flags']['hasFooterIncludeCode'] = true;
        return $ret;
    }

    public function getIncludeCode()
    {
        return $this->getData();
    }

    public function getTemplateVars(Kwf_Component_Renderer_Abstract $renderer)
    {
        $ret = parent::getTemplateVars($renderer);
        $ret['headline'] = $this->_getRow()->headline;
        $ret['text'] = $this->_getRow()->text;
        $ret['moreText'] = $this->_getRow()->more_text;
        $ret['acceptText'] = $this->_getRow()->accept_text;
        return $ret;
    }
}
