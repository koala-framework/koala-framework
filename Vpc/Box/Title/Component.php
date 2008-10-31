<?php
class Vpc_Box_Title_Component extends Vpc_Abstract
{
    protected function _getTitle()
    {
        $ret = $this->getData()->getTitle();
        if ($ret) $ret .= ' - ';
        $ret .= Zend_Registry::get('config')->application->name;
        return $ret;
    }
    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $ret['title'] = $this->_getTitle();
        return $ret;
    }

}
