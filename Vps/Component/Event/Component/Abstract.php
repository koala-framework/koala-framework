<?php
abstract class Vps_Component_Event_Component_Abstract extends Vps_Component_Event_Abstract
{
    public $dbId;

    public function __construct($componentClass, $dbId)
    {
        $this->class = $componentClass;
        $this->dbId = $dbId;
    }
}