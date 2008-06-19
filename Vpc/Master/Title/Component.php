<?php
class Vpc_Master_Title_Component extends Vpc_Master_Abstract
{
    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $ret['title'] = $this->getData()->getTitle() . " - "
            . Zend_Registry::get('config')->application->name;
        return $ret;
    }
}
