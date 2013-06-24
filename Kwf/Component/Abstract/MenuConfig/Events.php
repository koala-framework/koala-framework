<?php
class Kwf_Component_Abstract_MenuConfig_Events extends Kwf_Component_Events
{
    protected $_class;

    protected function _init()
    {
        $this->_class = $this->_config['componentClass'];
    }
}
