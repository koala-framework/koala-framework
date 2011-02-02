<?php
class Vps_Controller_Action_Auto_Filter_Query extends Vps_Controller_Action_Auto_Filter_Abstract
{
    const SELECT_TYPE_EQUALS = 'equals';
    const SELECT_TYPE_CONTAINS = 'contains';

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
            if (!$this->getSelectType() || $this->getSelectType() == self::SELECT_TYPE_EQUALS) {
                $select->whereEquals($column, $params[$this->getParamName()]);
            } else if ($this->getSelectType() == self::SELECT_TYPE_CONTAINS) {
                $select->where(new Vps_Model_Select_Expr_Contains($column, $params[$this->getParamName()]));
            }
        }
        return $select;
    }

    public function getName()
    {
        return $this->getFieldName();
    }
}
