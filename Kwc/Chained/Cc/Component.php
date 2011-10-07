<?php
class Kwc_Chained_Cc_Component extends Kwc_Chained_Abstract_Component
{
    public static function getSettings($masterComponentClass)
    {
        $ret = parent::getSettings();
        $copySettings = array('componentName', 'componentIcon', 'editComponents', 'viewCache', 'contentSender');
        $copyFlags = array('processInput', 'menuCategory', 'hasHome', 'chainedType', 'subroot', 'hasAlternativeComponent');
        $ret = Kwc_Chained_Abstract_Component::getChainedSettings($ret, $masterComponentClass, 'Cc', $copySettings, $copyFlags);
        return $ret;
    }

    public static function getChainedByMaster($masterData, $chainedData, $select = array())
    {
        return Kwc_Chained_Abstract_Component::_getChainedByMaster($masterData, $chainedData, 'Cc', $select);
    }
}
