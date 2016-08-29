<?php
class Kwf_Component_Cache_DynamicWithPartialId_Root_Component extends Kwc_Root_Abstract
{
    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);
        unset($ret['generators']['title']);
        unset($ret['generators']['box']);
        $ret['generators']['test'] = array(
            'class' => 'Kwf_Component_Generator_Page_Static',
            'component' => 'Kwf_Component_Cache_DynamicWithPartialId_TestComponent_Component',
            'name' => 'test'
        );
        return $ret;
    }
}