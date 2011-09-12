<?php
class Vps_Component_Generator_Events extends Vps_Component_Events
{
    protected function _getGenerator()
    {
        return Vps_Component_Generator_Abstract::getInstance(
            $this->_config['componentClass'], $this->_config['generatorKey']
        );
    }
}