<?php
class Vps_Component_Abstract_Events extends Vps_Component_Events
{
    protected $_class;

    protected function _init()
    {
        $this->_class = $this->_config['componentClass'];
    }
}
