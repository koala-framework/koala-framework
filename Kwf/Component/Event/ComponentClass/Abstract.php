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
    public $subroot;

    public function __construct($componentClass, Kwf_Component_Data $subroot = null)
    {
        $this->class = $componentClass;
        $this->subroot = $subroot;
    }
}