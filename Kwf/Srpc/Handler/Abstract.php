<?php
abstract class Vps_Srpc_Handler_Abstract
{
    protected $_extraParams;

    public function __construct()
    {
        $this->_init();
    }

    protected function _init()
    {
    }

    /**
     * Speichert extra-parameter, die individuell abgerufen werden müssen
     */
    final public function setExtraParams($params)
    {
        $this->_extraParams = $params;
    }

    final public function getExtraParams()
    {
        return $this->_extraParams;
    }
}
