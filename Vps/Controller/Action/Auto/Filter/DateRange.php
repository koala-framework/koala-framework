<?php
class Vps_Controller_Action_Auto_Filter_DateRange extends Vps_Controller_Action_Auto_Filter_Query
{
    protected $_type = 'DateRange';

    public function formatSelect($select, $params = array())
    {
        $field = $this->getParamName();

        if (isset($params[$field . '_from'])) {
            $valueFrom = $params[$field . '_from'];
        } else {
            $valueFrom = $this->getFrom();
        }

        if (isset($params[$field . '_to'])) {
            $valueTo = $params[$field . '_to'];
        } else {
            $valueTo = $this->getTo();
        }

        $field = $this->getFieldName();
        if ($valueFrom && $valueTo) {
            $select->where(new Vps_Model_Select_Expr_Or(array(
                new Vps_Model_Select_Expr_And(array(
                    new Vps_Model_Select_Expr_SmallerDate($field, $valueTo),
                    new Vps_Model_Select_Expr_HigherDate($field, $valueFrom)
                )),
                new Vps_Model_Select_Expr_Equals($field, $valueTo),
                new Vps_Model_Select_Expr_Equals($field, $valueFrom)
            )));
        } else if ($valueFrom) {
            $select->where(new Vps_Model_Select_Expr_Or(array(
                new Vps_Model_Select_Expr_HigherDate($field, $valueFrom),
                new Vps_Model_Select_Expr_Equals($field, $valueFrom)
            )));
        } else if ($valueTo) {
            $select->where(new Vps_Model_Select_Expr_Or(array(
                new Vps_Model_Select_Expr_SmallerDate($field, $valueTo),
                new Vps_Model_Select_Expr_Equals($field, $valueTo)
            )));
        }
        return $select;
    }

    public function getParamName()
    {
        return $this->getFieldName();
    }
}
