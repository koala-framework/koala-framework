<?php
class Kwc_Chained_Trl_Component extends Kwc_Chained_Abstract_Component
{
    public static function getSettings($masterComponentClass = null)
    {
        $ret = parent::getSettings($masterComponentClass);
        $copySettings = array('componentName', 'componentIcon', 'editComponents', 'viewCache', 'contentSender', 'plugins', 'pluginsInherit', 'masterTemplate');
        $copyFlags = array('processInput', 'menuCategory', 'chainedType', 'subroot', 'hasAlternativeComponent', 'resetMaster', 'noIndex', 'hasAnchors');
        $ret = Kwc_Chained_Abstract_Component::getChainedSettings($ret, $masterComponentClass, 'Trl', $copySettings, $copyFlags);
        return $ret;
    }

    public static function getChainedByMaster($masterData, $chainedData, $select = array())
    {
        return Kwc_Chained_Abstract_Component::_getChainedByMaster($masterData, $chainedData, 'Trl', $select);
    }

    public function getAnchors()
    {
        return $this->getData()->chained->getComponent()->getAnchors();
    }
}
