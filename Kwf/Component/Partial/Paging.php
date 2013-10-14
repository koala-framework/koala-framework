<?php
class Kwf_Component_Partial_Paging extends Kwf_Component_Partial_Abstract
{
    public function getIds()
    {
        $ret = array();
        $count = $this->getParam('count');
        $class = $this->getParam('class', false);
        if (!$class) { //paging deaktiviert
            for ($x = 0; $x < $count; $x++) {
                $ret[] = $x;
            }
        } else {
            $paramName = $this->getParam('paramName');
            $page = call_user_func(array($class, 'getCurrentPageByParam'), $paramName);
            $pagesize = $this->getParam('pagesize');
            $start = ($page - 1) * $pagesize;
            for ($x = $start; $x < $start + $pagesize; $x++) {
                if ($x < $count) $ret[] = $x;
            }
        }
        return $ret;
    }

    public static function useViewCache($componentId, $params)
    {
        if (!isset($params['paramName'])) return false;
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
