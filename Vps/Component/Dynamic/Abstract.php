<?php
abstract class Vps_Component_Dynamic_Abstract
{
    protected $_partialInfo;
    protected $_componentInfo;

    public function setPartialInfo($info)
    {
        $this->_partialInfo = $info;
    }
    public function setComponentInfo($info)
    {
        $this->_componentInfo = $info;
    }

    abstract public function getContent();
}
