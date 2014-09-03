<?php
class Kwf_Model_EventSubscriber extends Kwf_Events_Subscriber
{
    private $_model;

    protected function _init()
    {
    }

    protected function _getModel()
    {
        if (!isset($this->_model)) {
            $this->_model = Kwf_Model_Factory_Abstract::getModelInstance($this->_config['modelFactoryConfig']);
        }
        return $this->_model;
    }
}
