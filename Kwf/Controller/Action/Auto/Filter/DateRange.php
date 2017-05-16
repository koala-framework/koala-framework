<?php
class Kwf_Controller_Action_Auto_Filter_DateRange extends Kwf_Controller_Action_Auto_Filter_Query
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
            $select->where(new Kwf_Model_Select_Expr_Or(array(
                new Kwf_Model_Select_Expr_And(array(
                    new Kwf_Model_Select_Expr_Lower($field, new Kwf_Date($valueTo)),
                    new Kwf_Model_Select_Expr_Higher($field, new Kwf_Date($valueFrom))
                )),
                new Kwf_Model_Select_Expr_Equal($field, new Kwf_Date($valueTo)),
                new Kwf_Model_Select_Expr_Equal($field, new Kwf_Date($valueFrom))
            )));
        } else if ($valueFrom) {
            $select->where(new Kwf_Model_Select_Expr_Or(array(
                new Kwf_Model_Select_Expr_Higher($field, new Kwf_Date($valueFrom)),
                new Kwf_Model_Select_Expr_Equal($field, new Kwf_Date($valueFrom))
            )));
        } else if ($valueTo) {
            $select->where(new Kwf_Model_Select_Expr_Or(array(
                new Kwf_Model_Select_Expr_Lower($field, new Kwf_Date($valueTo)),
                new Kwf_Model_Select_Expr_Equal($field, new Kwf_Date($valueTo))
            )));
        }
        return $select;
    }

    public function getParamName()
    {
        return $this->getFieldName();
    }
}
