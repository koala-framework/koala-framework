<?php
class Kwc_Directories_List_EventRowAbstract extends Kwf_Component_Event_Row_Abstract
{
    public function __construct($class, Kwf_Model_Row_Abstract $row)
    {
        $this->class = $class;
        $this->row = $row;
    }
}
