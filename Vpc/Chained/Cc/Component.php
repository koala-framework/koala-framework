<?php
class Vpc_Chained_Cc_Component extends Vpc_Chained_Abstract_Component
{
    public static function getSettings($masterComponentClass)
    {
        $ret = parent::getSettings();
        $copySettings = array('componentName', 'componentIcon', 'editComponents', 'viewCache');
        $copyFlags = array('showInPageTreeAdmin', 'processInput', 'menuCategory', 'hasHome', 'chainedType', 'subroot');
        $ret = Vpc_Chained_Abstract_Component::getChainedSettings($ret, $masterComponentClass, 'Cc', $copySettings, $copyFlags);
        return $ret;
    }

    public static function getChainedByMaster($masterData, $chainedData, $select = array())
    {
        return Vpc_Chained_Abstract_Component::_getChainedByMaster($masterData, $chainedData, 'Cc', $select);
    }
}
