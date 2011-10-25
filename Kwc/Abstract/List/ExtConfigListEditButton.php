<?php
/**
 * List; list ist *nicht* immer sichtbar
 */
class Kwc_Abstract_List_ExtConfigListEditButton extends Kwf_Component_Abstract_ExtConfig_Abstract
{
    protected function _getConfig()
    {
        $detail = Kwc_Abstract::getChildComponentClass($this->_class, 'child');
        $gen = Kwf_Component_Generator_Abstract::getInstance($this->_class, 'child');
        $edit = Kwf_Component_Abstract_ExtConfig_Abstract::getEditConfigs($detail, $gen);
        $config = $this->_getStandardConfig('kwc.list.listEditButton', 'ListEditButton');
        $config['componentConfigs'] = $edit['componentConfigs'];
        $config['contentEditComponents'] = $edit['contentEditComponents'];
        $config['needsComponentPanel'] = true;
        return array(
            'list' => $config
        );
    }
}
