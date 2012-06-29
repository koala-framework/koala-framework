<?php
class Kwc_Directories_List_EventRowAbstract extends Kwf_Component_Event_Abstract
{
    public $itemId;
    public function __construct($class, $itemId)
    {
        parent::__construct($class);
        $this->itemId = $itemId;
    }
}
