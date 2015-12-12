<?php
class Kwc_Component extends Kwc_Abstract
{
    public function getTemplateVars(Kwf_Component_Renderer_Abstract $renderer = null)
    {
        $ret = parent::getTemplateVars($renderer);
        $ret['content'] = $this->_getContent();
        return $ret;
    }

    protected function _getContent()
    {
        return '';
    }
}
