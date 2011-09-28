<?php
abstract class Vps_Component_Abstract_ContentSender_Abstract
{
    protected $_data;
    public function __construct(Vps_Component_Data $data)
    {
        $this->_data = $data;
    }

    abstract public function sendContent();
}
