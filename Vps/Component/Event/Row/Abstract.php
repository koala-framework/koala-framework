<?php
abstract class Vps_Component_Event_Row_Abstract extends Vps_Component_Event_Abstract
{
    public $row;

    public function __construct(Vps_Model_Row_Abstract $row)
    {
        $this->class = get_class($row->getModel());
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
}