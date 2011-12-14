<?php
/**
 * @package Component
 * @subpackage Event
 */
class Kwf_Component_Event_Component_RecursiveAbstract extends Kwf_Component_Event_Abstract
{
    public $componentId;

    public function __construct($componentClass, $componentId)
    {
        $this->class = $componentClass;
        $this->componentId = $componentId;
    }
}
