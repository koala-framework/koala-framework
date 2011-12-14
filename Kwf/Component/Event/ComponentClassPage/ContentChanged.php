<?php
/**
 * @package Component
 * @subpackage Event
 */
class Kwf_Component_Event_ComponentClassPage_ContentChanged extends Kwf_Component_Event_ComponentClass_Abstract
{
    public $pageDbId;

    public function __construct($componentClass, $pageDbId)
    {
        $this->class = $componentClass;
        $this->pageDbId = $pageDbId;
    }
}