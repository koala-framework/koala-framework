<?php
class Kwc_Chained_Trl_Component extends Kwc_Chained_Abstract_Component
{
    public static function getSettings($masterComponentClass)
    {
        if (is_instance_of($masterComponentClass, 'Kwc_Chained_Abstract_Component')) {
            throw new Kwf_Exception("Cc of Cc shouldn't be used, called by '$masterComponentClass'");
        }

        $ret = parent::getSettings();
        $copySettings = array('componentName', 'componentIcon', 'editComponents', 'viewCache', 'contentSender');
        $copyFlags = array('processInput', 'menuCategory', 'chainedType', 'subroot', 'hasAlternativeComponent');
        $ret = Kwc_Chained_Abstract_Component::getChainedSettings($ret, $masterComponentClass, 'Trl', $copySettings, $copyFlags);
        return $ret;
    }

    public static function getChainedByMaster($masterData, $chainedData, $select = array())
    {
        return Kwc_Chained_Abstract_Component::_getChainedByMaster($masterData, $chainedData, 'Trl', $select);
    }
}
