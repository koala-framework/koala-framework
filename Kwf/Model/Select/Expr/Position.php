<?php
/**
 * calculate the position of a row in a model ordered by $field and optionally grouped by $groupBy
 */
class Kwf_Model_Select_Expr_Position implements Kwf_Model_Select_Expr_Interface
{
    const DIRECTION_ASC = 'asc';
    const DIRECTION_DESC = 'desc';

    private $_field;
    private $_groupBy;
    private $_direction;
    public function __construct($field, array $groupBy = array(), $dir = self::DIRECTION_DESC)
    {
        $this->_field = $field;
        $this->_groupBy = $groupBy;
        $this->_direction = (string)$dir;
    }

    public function getField()
    {
        return $this->_field;
    }

    public function getGroupBy()
    {
        return $this->_groupBy;
    }

    public function getDirection()
    {
        return $this->_direction;
    }

    public function validate()
    {
        if ($this->_direction != self::DIRECTION_ASC && $this->_direction != self::DIRECTION_DESC) {
            throw new Kwf_Exception("Invalid direction");
        }
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
            'groupBy' => $this->_groupBy,
            'direction' => $this->_direction
        );
    }

    public static function fromArray(array $data)
    {
        $cls = 'Kwf_Model_Select_Expr_'.$data['exprType'];
        return new $cls($data['field'], $data['groupBy'], $data['direction']);
    }
}
