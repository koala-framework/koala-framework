<?php
/**
 * @package Component
 * @subpackage Event
 */
class Kwf_Events_Event_Abstract
{
    /**
     * @var string
     */
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
        $ret = array();
        foreach (array_reverse(get_object_vars($this)) as $i) {
            if ($i instanceof Kwf_Component_Data) {
                $ret[] = $i->componentId;
            } else {
                $ret[] = (string)$i;
            }
        }
        return $ret;
    }
}
