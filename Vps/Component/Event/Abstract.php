<?php
class Vps_Component_Event_Abstract
{
    public $class;

    public function __toString()
    {
        $ret = str_replace('Vps_Component_Event_', '', get_class($this));
        $ret .= '(' . implode(', ', $this->_getVarsStringArray()) . ')';
        return $ret;
    }

    protected function _getVarsStringArray()
    {
        return array_reverse(get_object_vars($this));
    }
}