<?php
class Kwf_Component_Cache_ProcessInput_ContainsWithProcessInput_Component extends Kwc_Abstract_Composite_Component
{
    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);
        $ret['generators']['child']['component']['withProcessInput'] = 'Kwf_Component_Cache_ProcessInput_WithProcessInput_Component';
        return $ret;
    }
}
