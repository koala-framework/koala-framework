<?php
/**
 * List mit child daneben; list ist immer sichtbar
 */
class Vpc_Abstract_List_ExtConfigList extends Vps_Component_Abstract_ExtConfig_Abstract
{
    protected function _getConfig()
    {
        $detail = Vpc_Abstract::getChildComponentClass($this->_class, 'child');
        $gen = Vps_Component_Generator_Abstract::getInstance($this->_class, 'child');
        $edit = Vps_Component_Abstract_ExtConfig_Abstract::getEditConfigs($detail, $gen);
        $config = $this->_getStandardConfig('vpc.list.list');
        $config['componentConfigs'] = $edit['componentConfigs'];
        $config['contentEditComponents'] = $edit['contentEditComponents'];
        $config['useInsertAdd'] = true;
        $config['listTitle'] = null;
        return array(
            'list' => $config
        );
    }
}