<?php
class Vps_Component_Generator_Events extends Vps_Component_Events
{
    protected $_class;

    protected function _init()
    {
        $this->_class = $this->_config['componentClass'];
    }

    protected function _getGenerator()
    {
        return Vps_Component_Generator_Abstract::getInstance(
            $this->_config['componentClass'], $this->_config['generatorKey']
        );
    }
}