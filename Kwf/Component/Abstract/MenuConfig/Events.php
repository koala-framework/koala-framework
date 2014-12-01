<?php
class Kwf_Component_Abstract_MenuConfig_Events extends Kwf_Events_Subscriber
{
    protected $_class;

    protected function _init()
    {
        $this->_class = $this->_config['componentClass'];
    }
}
