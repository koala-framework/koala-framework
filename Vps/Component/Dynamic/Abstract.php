<?php
abstract class Vps_Component_Dynamic_Abstract
{
    protected $_info;
    protected $_componentInfo;

    public function setInfo($info)
    {
        $this->_info = $info;
    }

    public function setArguments() {}

    abstract public function getContent();
}
