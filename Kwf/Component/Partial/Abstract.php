<?php
class Kwf_Component_Partial_Abstract
{
    protected $_params;

    public function __construct($params)
    {
        $this->_params = $params;
    }

    public function getIds()
    {
        return array();
    }

    public function getParam($param, $necessary = true)
    {
        if ($necessary && !isset($this->_params[$param]))
            throw new Kwf_Exception('Param needed for Partial: ' . $param);
        if (!isset($this->_params[$param])) return null;
        return $this->_params[$param];
    }

    public static function useViewCache()
    {
        return false;
    }
}
