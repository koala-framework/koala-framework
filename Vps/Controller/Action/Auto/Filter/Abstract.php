<?php
abstract class Vps_Controller_Action_Auto_Filter_Abstract
{
    protected $_defaults = array();
    protected $_config;

    public function __construct($config = array())
    {
        $this->_init();
        foreach ($config as $key => $val) {
            $this->_config[$key] = $val;
        }
        foreach ($this->_defaults as $key => $val) {
            if (!isset($this->_config[$key])) {
                if (is_null($val))
                    throw new Vps_Exception("Parameter '$key' ist needed for Filter " . get_class($this));
                $this->_config[$key] = $val;
            }
        }
    }

    protected function _init() {}

    public function getConfig($key)
    {
        if (!isset($this->_config[$key])) return null;
        return $this->_config[$key];
    }

    public function isSkipWhere()
    {
        return $this->getConfig('skipWhere') === true;
    }

    abstract public function formatSelect($select, $query = array());

    public function getExtConfig()
    {
        $ret = $this->_config;
        $ret['type'] = ucfirst(substr(strrchr(get_class($this), '_'), 1));
        $ret['id'] = $this->getId();
        $ret['paramName'] = $this->getParamName();
        return $ret;
    }

    public function getParamName()
    {
        return 'query_' . $this->getId();
    }
}
