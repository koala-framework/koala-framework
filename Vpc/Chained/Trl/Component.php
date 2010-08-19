<?php
class Vpc_Chained_Trl_Component extends Vpc_Chained_Abstract_Component
{
    public static function getSettings($masterComponentClass)
    {
        $ret = parent::getSettings();
        $copySettings = array('componentName', 'componentIcon', 'editComponents', 'chainedType');
        $copyFlags = array('showInPageTreeAdmin', 'processInput', 'menuCategory');
        $ret = Vpc_Chained_Abstract_Component::getChainedSettings($ret, $masterComponentClass, 'Trl', $copySettings, $copyFlags);
        return $ret;
    }

    public static function getChainedByMaster($masterData, $chainedData, $select = array())
    {
        return Vpc_Chained_Abstract_Component::_getChainedByMaster($masterData, $chainedData, 'Trl', $select);
    }
}
