<?php
abstract class Vps_Component_Plugin_Abstract extends Vps_Component_Abstract
{
    protected $_componentId;

    public function __construct($componentId)
    {
        parent::__construct($componentId);
        $this->_componentId = $componentId;
    }
}
