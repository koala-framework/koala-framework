<?php
class Kwc_Directories_List_EventItemAbstract extends Kwc_Directories_List_EventAbstract
{
    public $itemId;
    public function __construct($class, $itemId, $subroot)
    {
        parent::__construct($class, $subroot);
        $this->itemId = $itemId;
    }
}
