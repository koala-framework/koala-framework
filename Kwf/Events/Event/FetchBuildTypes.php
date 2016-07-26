<?php
class Kwf_Events_Event_FetchBuildTypes extends Kwf_Events_Event_Abstract
{
    public $types = array();

    public function addType(Kwf_Util_Build_Types_Abstract $type)
    {
        $this->types[] = $type;
    }
}
