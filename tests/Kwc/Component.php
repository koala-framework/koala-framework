<?php
class Vpc_Component extends Vpc_Abstract
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
