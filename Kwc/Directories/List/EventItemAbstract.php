<?php
class Kwc_Directories_List_EventItemAbstract extends Kwf_Events_Event_Abstract
{
    public $itemId;
    public $subroot;
    public function __construct($class, $itemId, $subroot)
    {
        parent::__construct($class);
        $this->itemId = $itemId;
        $this->subroot = $subroot;
    }
}
