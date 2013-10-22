<?php
class Kwc_Chained_Trl_Component extends Kwc_Chained_Abstract_Component
{
    public static function getSettings($masterComponentClass)
    {
        $ret = parent::getSettings();
        $copySettings = array('componentName', 'componentIcon', 'editComponents', 'viewCache', 'contentSender', 'plugins', 'masterTemplate', 'resetMaster');
        $copyFlags = array('processInput', 'menuCategory', 'chainedType', 'subroot', 'hasAlternativeComponent');
        $ret = Kwc_Chained_Abstract_Component::getChainedSettings($ret, $masterComponentClass, 'Trl', $copySettings, $copyFlags);
        return $ret;
    }

    public static function getChainedByMaster($masterData, $chainedData, $select = array())
    {
        return Kwc_Chained_Abstract_Component::_getChainedByMaster($masterData, $chainedData, 'Trl', $select);
    }
}
