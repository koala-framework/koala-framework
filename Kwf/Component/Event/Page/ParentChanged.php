<?php
/**
 * @package Component
 * @subpackage Event
 */
class Kwf_Component_Event_Page_ParentChanged extends Kwf_Component_Event_Component_RecursiveAbstract
{
    /**
     * @var Kwf_Component_Data
     */
    public $oldParent;

    /**
     * @var Kwf_Component_Data
     */
    public $newParent;

    public function __construct($componentClass, Kwf_Component_Data $component, Kwf_Component_Data $newParent, Kwf_Component_Data $oldParent)
    {
        $this->oldParent = $oldParent;
        $this->newParent = $newParent;
        parent::__construct($componentClass, $component);
    }
}
