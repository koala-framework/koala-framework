<?php
class Vps_Component_Cache_DynamicWithPartialId_Root_Component extends Vpc_Root_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        unset($ret['generators']['title']);
        unset($ret['generators']['box']);
        $ret['generators']['test'] = array(
            'class' => 'Vps_Component_Generator_Page_Static',
            'component' => 'Vps_Component_Cache_DynamicWithPartialId_TestComponent_Component',
            'name' => 'test'
        );
        return $ret;
    }
}