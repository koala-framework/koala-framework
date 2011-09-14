<?php
abstract class Vps_Component_Event_Row_Abstract extends Vps_Component_Event_Abstract
{
    public $row;

    public function __construct(Vps_Model_Row_Abstract $row)
    {
        $this->class = get_class($row->getModel());
        $this->row = $row;
    }
}