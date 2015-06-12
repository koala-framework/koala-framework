<?php
/**
 * @package Component
 * @subpackage Event
 */
class Kwf_Component_Event_ComponentClass_Tag_PartialChanged extends Kwf_Component_Event_ComponentClass_Tag_Abstract
{
    public $id;

    public function __construct($viewClass, $tag, $partialId)
    {
        parent::__construct($viewClass, $tag);
        $this->id = $partialId;
    }
}
