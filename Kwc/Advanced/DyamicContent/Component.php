<?php
/**
 * Component that allows dynamic content without having to disable view cache.
 */
abstract class Kwc_Advanced_DyamicContent_Component extends Kwc_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['plugins'][] = 'Kwc_Advanced_DyamicContent_Plugin';
        return $ret;
    }

    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $language = $this->getData()->getLanguage();
        $ret['dynamicPlaceholder'] = '{dynamicContent '.$this->getData()->componentClass.' '. $language .'}';
        return $ret;
    }

    abstract public static function getDynamicContent($componentId, $componentClass);
}
