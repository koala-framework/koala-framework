<?php
class Kwf_Component_Event_ComponentClass_Abstract extends Kwf_Component_Event_Abstract
{
    public function __construct($componentClass)
    {
        $this->class = $componentClass;
    }
}