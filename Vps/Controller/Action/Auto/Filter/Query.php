<?php
class Vps_Controller_Action_Auto_Filter_Query extends Vps_Controller_Action_Auto_Filter_Abstract
{
    protected function _init()
    {
        parent::_init();
        $this->_mandatoryProperties[] = 'model';
        $this->_mandatoryProperties[] = 'fieldName';
    }

    public function formatSelect($select, $params = array()) {
        $column = $this->getFieldName();
        if (!$this->getModel()->hasColumn($column)) {
            throw new Vps_Exception("Model has to have column \"$column\" to filter");
        }
        if (isset($params[$this->getParamName()]) && $params[$this->getParamName()]) {
            $select->whereEquals($column, $params[$this->getParamName()]);
        }
        return $select;
    }

    public function getName()
    {
        return $this->getFieldName();
    }
}
