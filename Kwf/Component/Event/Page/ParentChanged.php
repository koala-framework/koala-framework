<?php
class Kwf_Component_Event_Page_ParentChanged extends Kwf_Component_Event_Component_RecursiveAbstract
{
    public $oldParentId;
    public $newParentId;

    public function __construct($componentClass, $componentId, $newParentId, $oldParentId)
    {
        $this->oldParentId = $oldParentId;
        $this->newParentId = $newParentId;
        parent::__construct($componentClass, $componentId);
    }
}