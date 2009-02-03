<?php
class Vps_Component_Partial_Random extends Vps_Component_Partial_Abstract
{
    public function getIds()
    {
        $limit = $this->getParam('limit');
        $count = $this->getParam('count');
        if ($limit >= $count) $limit = $count;
        $random = array();
        while(count($random) < $limit) {
            $r = rand(0, $count - 1);
            $random[$r] = true;
        }
        return array_keys($random);
    }
}
