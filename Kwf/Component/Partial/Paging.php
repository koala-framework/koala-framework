<?php
class Vps_Component_Partial_Paging extends Vps_Component_Partial_Abstract
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
}
