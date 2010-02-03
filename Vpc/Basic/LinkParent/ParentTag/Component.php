<?php
class Vpc_Basic_LinkParent_ParentTag_Component extends Vpc_Abstract
{
    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $ret['parentPageUrl'] = $this->_getParentPageUrl();
        return $ret;
    }

    protected function _getParentPageUrl()
    {
        return $this->getData()->getPage()->parent->url;
    }

    public function hasContent()
    {
        if ($this->_getParentPageUrl()) return true;
        return false;
    }
}
