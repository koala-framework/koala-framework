<?php
/**
 * List; list ist *nicht* immer sichtbar
 */
class Vpc_Abstract_List_ExtConfigListEditButton extends Vps_Component_Abstract_ExtConfig_Abstract
{
    protected function _getConfig()
    {
        $detail = Vpc_Abstract::getChildComponentClass($this->_class, 'child');
        $gen = Vps_Component_Generator_Abstract::getInstance($this->_class, 'child');
        $generators = Vpc_Abstract::getSetting($this->_class, 'generators');
        $idTemplate = '{componentId}'.$gen->getIdSeparator().'{0}';
        $edit = Vps_Component_Abstract_ExtConfig_Abstract::getEditConfigs($detail, $gen, $idTemplate, '');

        $config = $this->_getStandardConfig('vpc.list.listEditButton', 'ListEditButton');
        $config['componentConfigs'] = $edit['componentConfigs'];
        $config['contentEditComponents'] = $edit['contentEditComponents'];
        $config['needsComponentPanel'] = true;
        return array(
            'list' => $config
        );
    }
}
