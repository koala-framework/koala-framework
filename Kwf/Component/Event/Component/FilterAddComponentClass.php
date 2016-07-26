<?php
class Kwf_Component_Event_Component_FilterAddComponentClass extends Kwf_Events_Event_Abstract
{
    public $componentOrParent;
    public $deny = false;

    public function __construct($class, $componentOrParent)
    {
        parent::__construct($class);
        $this->componentOrParent = $componentOrParent;
    }
}
