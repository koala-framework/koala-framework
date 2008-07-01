<?php
abstract class Vps_Component_Plugin_Abstract
{
    protected $_componentId;

    public function __construct($componentId)
    {
        $this->_componentId = $componentId;
    }
}
