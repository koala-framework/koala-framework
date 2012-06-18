<?php
/**
 * calculate the position of a row in a model ordered by $field and optionally grouped by $groupBy
 */
class Kwf_Model_Select_Expr_Position implements Kwf_Model_Select_Expr_Interface
{
    private $_field;
    private $_groupBy;
    public function __construct($field, array $groupBy = array())
    {
        $this->_field = $field;
        $this->_groupBy = $groupBy;
    }

    public function getField()
    {
        return $this->_field;
    }

    public function getGroupBy()
    {
        return $this->_groupBy;
    }

    public function validate()
    {
    }

    public function getResultType()
    {
        return Kwf_Model_Interface::TYPE_INTEGER;
    }

    public function toArray()
    {
        return array(
            'exprType' => str_replace('Kwf_Model_Select_Expr_', '', get_class($this)),
            'field' => $this->_field,
            'groupBy' => $this->_groupBy
        );
    }

    public static function fromArray(array $data)
    {
        $cls = 'Kwf_Model_Select_Expr_'.$data['exprType'];
        return new $cls($data['field'], $data['groupBy']);
    }
}
