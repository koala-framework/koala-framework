<?php
class Kwf_Util_ClearCache_Types_Events extends Kwf_Util_ClearCache_Types_Abstract
{
    //TODO clear?

    protected function _refreshCache($options)
    {
        Kwf_Component_Events::getAllListeners();
    }

    public function getTypeName()
    {
        return 'events';
    }
    public function doesRefresh() { return true; }
    public function doesClear() { return false; }
}
