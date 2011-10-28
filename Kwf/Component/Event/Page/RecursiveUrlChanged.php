<?php
class Kwf_Component_Event_Page_RecursiveUrlChanged extends Kwf_Component_Event_Abstract
{
    public $componentId;

    public function __construct($componentClass, $componentId)
    {
        $this->class = $componentClass;
        $this->componentId = $componentId;
    }
}