<?php
/**
 * @package Component
 * @subpackage Event
 */
class Kwf_Events_Event_Media_Changed extends Kwf_Events_Event_Abstract
{
    public $component;
    public $type;

    public function __construct($componentClass, Kwf_Component_Data $component, $type = 'default')
    {
        $this->class = $componentClass;
        $this->component = $component;
        $this->type = $type;
    }
}
