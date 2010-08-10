<?php
class Vps_Component_Generator_Plugin_StatusUpdate_ExtConfig extends Vps_Component_Abstract_ExtConfig_Abstract
{
    protected function _getConfig()
    {
        $config = $this->_getStandardConfig('vps.component.generator.plugin.statusUpdate', 'SendForm');
        $config['logControllerUrl'] = $this->getControllerUrl('Log');
        $config['backends'] = array();
        foreach ($this->_getSetting('backends') as $b=>$c) {
            $backend = new $c('');
            $config['backends'][] = array(
                'name' => $b,
                'label' => $backend->getName()
            );
        }
        return array(
            'form' => $config
        );
    }
}
