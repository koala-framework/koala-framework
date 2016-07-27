<?php
class Kwf_Events_Event_FetchClearCacheTypes extends Kwf_Events_Event_Abstract
{
    public $types = array();

    public function addType(Kwf_Util_ClearCache_Types_Abstract $type)
    {
        $this->types[] = $type;
    }
}
