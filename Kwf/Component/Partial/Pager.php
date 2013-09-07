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

    public static function useViewCache($componentId, $params)
    {
        return array(
            'callback' => array(
                'Kwf_Component_Partial_Paging',
                '_useViewCacheDynamic'
            ),
            'args' => array(
                $params['paramName']
            )
        );
    }

    public function _useViewCacheDynamic($paramName)
    {
        if (!isset($_GET[$paramName]) || $_GET[$paramName]==1) {
            return true;
        }
        return false;
    }
}
