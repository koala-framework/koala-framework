<?php
class Kwf_Component_Cache_ProcessInput_ContainsWithProcessInput_Component extends Kwc_Abstract_Composite_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['child']['component']['withProcessInput'] = 'Kwf_Component_Cache_ProcessInput_WithProcessInput_Component';
        return $ret;
    }
}
