<?php
class Vps_Component_Event_Page_RecursiveFilenameChanged extends Vps_Component_Event_Abstract
{
    public $componentId;

    public function __construct($componentClass, $componentId)
    {
        $this->class = $componentClass;
        $this->componentId = $componentId;
    }
}