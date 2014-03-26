<?php
class Kwf_Component_Partial_Paging extends Kwf_Component_Partial_Abstract
{
    public function getIds()
    {
        $ret = array();
        $count = $this->getParam('count');
        $paging = $this->getParam('paging', false);
        if (!$paging) { //paging deaktiviert
            for ($x = 0; $x < $count; $x++) {
                $ret[] = $x;
            }
        } else {
            $page = call_user_func(array($paging['class'], 'getCurrentPageByParam'), $paging['paramName']);
            $start = ($page - 1) * $paging['pagesize'];
            for ($x = $start; $x < $start + $paging['pagesize']; $x++) {
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
