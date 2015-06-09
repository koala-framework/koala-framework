<?php
/**
 * @package Component
 * @subpackage Event
 */
abstract class Kwf_Events_Event_Row_Abstract extends Kwf_Events_Event_Abstract
{
    public $row;

    public function __construct(Kwf_Model_Row_Abstract $row)
    {
        $this->class = $row->getModel()->getFactoryId();
        $this->row = $row;
    }

    public function isDirty($fieldnames)
    {
        if (!is_array($fieldnames)) $fieldnames = array($fieldnames);
        $dc = $this->row->getDirtyColumns();
        foreach ($fieldnames as $fieldname) {
            if (in_array($fieldname, $dc)) return true;
        }
        return false;
    }

    protected function _getVarsStringArray()
    {
        return array(
            $this->class,
            $this->row->{$this->row->getModel()->getPrimaryKey()}
        );
    }
}
