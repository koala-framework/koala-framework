<?php
class Vps_Component_Partial_Id extends
    Vps_Component_Partial_Paging
{
    public function getIds()
    {
        $ret = array();
        $class = $this->getParam('class', false);
        $paramName = $this->getParam('paramName', false);
        $component = Vps_Component_Data_Root::getInstance()->getComponentById($this->getParam('componentId'));
        $count = null;
        $offset = null;
        if ($class && $paramName) {
            $page = call_user_func(array($class, 'getCurrentPageByParam'), $paramName);
            $count = $this->getParam('pagesize');
            $offset = (($page - 1) * $count);
        }
        if (!$component) return array();
        return $component->getComponent()->getItemIds($count, $offset);
    }

}