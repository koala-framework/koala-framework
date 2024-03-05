<?php
class Kwc_Chained_Cc_Component extends Kwc_Chained_Abstract_Component
{
    public static function getSettings($masterComponentClass = null)
    {
        $ret = parent::getSettings($masterComponentClass);
        $copySettings = array('componentName', 'componentIcon', 'editComponents', 'viewCache', 'contentSender', 'plugins', 'pluginsInherit', 'masterTemplate', 'resetMaster');
        $copyFlags = array('processInput', 'menuCategory', 'hasHome', 'chainedType', 'subroot', 'hasAlternativeComponent', 'hasFulltext', 'skipFulltext', 'skipFulltextRecursive');
        $ret = Kwc_Chained_Abstract_Component::getChainedSettings($ret, $masterComponentClass, 'Cc', $copySettings, $copyFlags);
        return $ret;
    }

    public static function createChainedGenerator($class, $key, $prefix = 'Cc')
    {
        return parent::createChainedGenerator($class, $key, $prefix);
    }

    public static function getChainedByMaster($masterData, $chainedData, $select = array())
    {
        return Kwc_Chained_Abstract_Component::_getChainedByMaster($masterData, $chainedData, 'Cc', $select);
    }

    public function getFulltextContent()
    {
        return $this->getData()->chained->getComponent()->getFulltextContent();
    }
}
