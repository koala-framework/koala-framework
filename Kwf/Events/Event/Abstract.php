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
            } else if (is_array($i)) {
                $ret[] = json_encode($i);
            } else {
                $ret[] = (string)$i;
            }
        }
        return $ret;
    }

    protected function _getClassFromRow($classes, $row, $cleanValue = false)
    {
        if (count($classes) > 1 && $row->getModel()->hasColumn('component')) {
            if ($cleanValue) {
                $c = $row->getCleanValue('component');
            } else {
                $c = $row->component;
            }
            if (isset($classes[$c])) {
                return $classes[$c];
            }
        }
        $class = array_shift($classes);
        return $class;
    }
}
