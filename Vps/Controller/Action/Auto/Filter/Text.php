<?php
class Vps_Controller_Action_Auto_Filter_Text extends Vps_Controller_Action_Auto_Filter_Abstract
{
    protected $_queryFields;
    protected $_querySeparator = ' ';
    protected $_model;

    public function formatSelect($select, $params = array())
    {
        if (!isset($params['query']) || !$params['query']) return $select;

        if (!$this->_queryFields) {
            throw new Vps_Exception("queryFields which is required to use Filter_Text is not set.");
        }

        $query = str_replace(': ', ':', $params['query']);
        if ($this->_querySeparator) {
            $query = explode($this->_querySeparator, $query);
        } else {
            $query = array($query);
        }

        foreach ($query as $q) {
            if (strpos($q, ':') !== false) { // falls nach einem bestimmten feld gesucht wird zB id:15
                $whereContainsColon = $this->_getQueryContainsColon($q);
                if (!is_null($whereContainsColon)) {
                    $select->where($whereContainsColon);
                } else {
                    $select->where($this->_getQueryExpression($q));
                }
            } else {
                $select->where($this->_getQueryExpression($q));
            }
        }
        return $select;
    }

    private function _getQueryContainsColon($query)
    {
        if (!$this->_model) throw new Vps_Exception('Model has to be set for Text-Filter');
        $availableColumns = $this->_model->getColumns();

        list($field, $value) = explode(':', $query);
        if (in_array($field, $availableColumns)) {
            if (is_numeric($value)) {
                return new Vps_Model_Select_Expr_Equals($field, $value);
            } else {
                return new Vps_Model_Select_Expr_Contains($field, $value);
            }
        } else {
            return null;
        }
    }

    private function _getQueryExpression($query)
    {
        $containsExpression = array();
        foreach ($this->_queryFields as $queryField) {
            $containsExpression[] = new Vps_Model_Select_Expr_Contains($queryField, $query);
        }
        return new Vps_Model_Select_Expr_Or($containsExpression);
    }

}
