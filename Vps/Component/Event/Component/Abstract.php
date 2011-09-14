<?php
abstract class Vps_Component_Event_Component_Abstract extends Vps_Component_Event_Abstract
{
    public $row;

    public function __construct($componentClass, $row)
    {
        $this->class = $componentClass;
        $this->row = $row;
    }
}