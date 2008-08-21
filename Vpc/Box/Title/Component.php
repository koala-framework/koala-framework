<?php
class Vpc_Box_Title_Component extends Vpc_Abstract
{
    protected function _getTitle()
    {
        return $this->getData()->getTitle() . " - "
            . Zend_Registry::get('config')->application->name;
    }
    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $ret['title'] = $this->_getTitle();
        return $ret;
    }

}
