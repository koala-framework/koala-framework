<?php
abstract class Kwc_Directories_List_EventAbstract extends Kwf_Events_Event_Abstract
{
    public $subroot;
    public function __construct($class, $subroot = null)
    {
        parent::__construct($class);
        $this->subroot = $subroot;
    }
}
