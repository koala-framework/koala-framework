<?php
class Kwf_Component_Partial_Pager extends Kwf_Component_Partial_Abstract
{
    public function getIds()
    {
        $class = $this->getParam('class');
        $paramName = $this->getParam('paramName');
        $page = call_user_func(array($class, 'getCurrentPageByParam'), $paramName);
        return array($page);
    }
}
