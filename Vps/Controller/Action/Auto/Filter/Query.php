<?php
class Vps_Controller_Action_Auto_Filter_Query extends Vps_Controller_Action_Auto_Filter_Abstract
{
    protected function _init()
    {
        parent::_init();
        $this->_defaults['model'] = null;
        $this->_defaults['fieldname'] = null;
    }

    public function formatSelect($select, $params = array()) {
        $column = $this->getFieldname();
        if (!$this->getConfig('model')->hasColumn($column))
            throw new Vps_Exception("Model has to have column \"$column\" to filter");
        if (isset($params[$this->getParamName()]) && $params[$this->getParamName()]) {
            $select->whereEquals($column, $params[$this->getParamName()]);
        }
        return $select;
    }

    public function getExtConfig()
    {
        $ret = parent::getExtConfig();
        unset($ret['model']);
        return $ret;
    }

    public function getFieldname()
    {
        return $this->getConfig('fieldname');
    }

    public function getId()
    {
        return $this->getFieldname();
    }
}
