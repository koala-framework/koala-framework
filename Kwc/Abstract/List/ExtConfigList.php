<?php
/**
 * List mit child daneben; list ist immer sichtbar
 */
class Kwc_Abstract_List_ExtConfigList extends Kwf_Component_Abstract_ExtConfig_Abstract
{
    protected function _getConfig()
    {
        $detail = Kwc_Abstract::getChildComponentClass($this->_class, 'child');
        $gen = Kwf_Component_Generator_Abstract::getInstance($this->_class, 'child');
        $edit = Kwf_Component_Abstract_ExtConfig_Abstract::getEditConfigs($detail, $gen);
        $config = $this->_getStandardConfig('kwc.list.list');
        $config['componentConfigs'] = $edit['componentConfigs'];
        $config['contentEditComponents'] = $edit['contentEditComponents'];
        $config['useInsertAdd'] = true;
        $config['listTitle'] = null;
        return array(
            'list' => $config
        );
    }
}