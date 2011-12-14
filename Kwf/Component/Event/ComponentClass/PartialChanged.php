<?php
/**
 * @package Component
 * @subpackage Event
 */
class Kwf_Component_Event_ComponentClass_PartialChanged extends Kwf_Component_Event_Abstract
{
    public $id;

    public function __construct($viewClass, $partialId)
    {
        parent::__construct($viewClass);
        $this->id = $partialId;
    }
}