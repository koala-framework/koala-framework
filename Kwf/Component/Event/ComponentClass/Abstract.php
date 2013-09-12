<?php
/**
 * @package Component
 * @subpackage Event
 */
class Kwf_Component_Event_ComponentClass_Abstract extends Kwf_Component_Event_Abstract
{
    /**
     * @var Kwf_Component_Data
     */
    public $component;

    public function __construct($componentClass, Kwf_Component_Data $component = null)
    {
        $this->class = $componentClass;
        $this->component = $component;
    }
}