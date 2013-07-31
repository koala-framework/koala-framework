<?php
class Kwf_Component_Partial_Stateless extends Kwf_Component_Partial_Paging
{
    public function getIds()
    {
        $ret = array();
        $count = $this->getParam('count');
        for ($x = 0; $x < $count; $x++) {
            $ret[] = $x;
        }
        return $ret;
    }

    public static function useViewCache()
    {
        return true;
    }
}
