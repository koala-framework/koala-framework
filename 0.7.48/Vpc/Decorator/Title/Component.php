<?php
class Vpc_Decorator_Title_Component extends Vpc_Decorator_Abstract
{
    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $pc = $this->getPageCollection();
        $ret['title'] = $pc->getTitle($this) . " - "
            . Zend_Registry::get('config')->application->name;
        return $ret;
    }
}
