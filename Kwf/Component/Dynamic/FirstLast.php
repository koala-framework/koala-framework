<?php
class Vps_Component_Dynamic_FirstLast extends Vps_Component_Dynamic_Abstract
{
    public function setArguments()
    {
    }

    public function getContent()
    {
        $ret = '';
        $info = $this->_info;
        if ($info['number'] == 0) {
            $ret .= ' first';
        }
        if ($info['number'] == $info['total']-1) {
            $ret .= ' last';
        }
        return trim($ret);
    }
}
