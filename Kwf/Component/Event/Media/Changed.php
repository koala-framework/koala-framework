<?php
/**
 * @package Component
 * @subpackage Event
 */
class Kwf_Component_Event_Media_Changed extends Kwf_Component_Event_Abstract
{
    public $componentId;
    public $type;

    public function __construct($componentClass, $componentId, $type = 'default')
    {
        $this->class = $componentClass;
        $this->componentId = $componentId;
        $this->type = $type;
    }
}