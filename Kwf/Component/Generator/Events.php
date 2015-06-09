<?php
class Kwf_Component_Generator_Events extends Kwf_Events_Subscriber
{
    protected $_class;

    protected function _init()
    {
        $this->_class = $this->_config['componentClass'];
    }

    protected function _getGenerator()
    {
        return Kwf_Component_Generator_Abstract::getInstance(
            $this->_config['componentClass'], $this->_config['generatorKey']
        );
    }
}