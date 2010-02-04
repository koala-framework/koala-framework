<?php
class Vpc_Basic_LinkTag_ParentPage_Component extends Vpc_Abstract
{
    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $parentUrl = $this->_getParentPageUrl();
        $ret['parentPageUrl'] = $parentUrl ? $parentUrl : '';
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
