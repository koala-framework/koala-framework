<?php
class Kwc_Component extends Kwc_Abstract
{
    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $ret['content'] = $this->_getContent();
        return $ret;
    }

    protected function _getContent()
    {
        return '';
    }
}
