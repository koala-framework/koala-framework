<?php
/**
 * @package Component
 * @subpackage Event
 */
class Kwf_Component_Event_ComponentClassPage_ContentChanged extends Kwf_Component_Event_ComponentClass_Abstract
{
    public $page;

    public function __construct($componentClass, Kwf_Component_Data $page)
    {
        $this->class = $componentClass;
        $this->page = $page;
    }
}