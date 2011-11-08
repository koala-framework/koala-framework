<?php
class Kwf_Controller_Action_Auto_Filter_Text extends Kwf_Controller_Action_Auto_Filter_Abstract
{
    protected $_type = 'Text';

    protected $_defaultPropertyValues = array(
        'querySeparator' => ' ',
    );
    protected $_mandatoryProperties = array('queryFields');

    public function formatSelect($select, $params = array())
    {
        if (!isset($params['query']) || !$params['query']) return $select;

        $query = str_replace(': ', ':', $params['query']);
        if ($this->getQuerySeparator()) {
            $query = explode($this->getQuerySeparator(), $query);
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
        $model = $this->getModel();
        list($field, $value) = explode(':', $query);
        if ($model->hasColumn($field)) {
            if (is_numeric($value)) {
                return new Kwf_Model_Select_Expr_Equal($field, $value);
            } else {
                return new Kwf_Model_Select_Expr_Contains($field, $value);
            }
        } else {
            return null;
        }
    }

    protected function _getQueryExpression($query)
    {
        $containsExpression = array();
        foreach ($this->getQueryFields() as $queryField) {
            $containsExpression[] = new Kwf_Model_Select_Expr_Contains($queryField, $query);
        }
        return new Kwf_Model_Select_Expr_Or($containsExpression);
    }

    public function getExtConfig()
    {
        $ret = parent::getExtConfig();
        unset($ret['queryFields']);
        return $ret;
    }

    public function getName()
    {
        return 'text';
    }

    public function getParamName()
    {
        return 'query';
    }
}
