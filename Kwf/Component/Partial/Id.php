<?php
class Kwf_Component_Partial_Id extends
    Kwf_Component_Partial_Paging
{
    public function getIds()
    {
        $ret = array();
        $component = Kwf_Component_Data_Root::getInstance()->getComponentById($this->getParam('componentId'));
        $count = null;
        $offset = null;
        $paging = $this->getParam('paging', false);
        if ($paging) {
            $page = call_user_func(array($paging['class'], 'getCurrentPageByParam'), $paging['paramName']);
            $count = $paging['pagesize'];
            $offset = (($page - 1) * $count);
        }
        if (!$component) return array();
        return $component->getComponent()->getItemIds($count, $offset);
    }

    public static function useViewCache($componentId, $params)
    {
        return false;
    }
}
