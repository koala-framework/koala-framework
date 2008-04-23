<?php
class Vpc_Decorator_Title_Component extends Vpc_Decorator_Abstract
{
    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $ret['title'] = $this->getTreeCacheRow()->getTitle() . " - "
            . Zend_Registry::get('config')->application->name;
        return $ret;
    }
}
