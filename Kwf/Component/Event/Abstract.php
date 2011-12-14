<?php
/**
 * @package Component
 * @subpackage Event
 */
class Kwf_Component_Event_Abstract
{
    public $class;

    public function __construct($class)
    {
        $this->class = $class;
    }

    public function __toString()
    {
        $ret = str_replace('Kwf_Component_Event_', '', get_class($this));
        $ret .= '(' . implode(', ', $this->_getVarsStringArray()) . ')';
        return $ret;
    }

    protected function _getVarsStringArray()
    {
        return array_reverse(get_object_vars($this));
    }
}