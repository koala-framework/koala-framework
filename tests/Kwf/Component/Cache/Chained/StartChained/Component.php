<?php
class Kwf_Component_Cache_Chained_StartChained_Component extends Kwc_Chained_Start_Component
{
    public static function getSettings($masterComponentClass = null)
    {
        $ret = parent::getSettings($param);
        $copySettings = array('editComponents');
        $copyFlags = array('subroot');
        $ret = Kwc_Chained_Abstract_Component::getChainedSettings($ret, $masterComponentClass, 'Trl', $copySettings, $copyFlags);
        $ret['flags']['chainedType'] = 'Trl';
        return $ret;
    }
}
