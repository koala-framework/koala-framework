<?php
class Kwf_Model_EventSubscriber extends Kwf_Events_Subscriber
{
    protected $_modelClass;

    protected function _init()
    {
        $this->_modelClass = $this->_config['modelClass'];
    }
}
