<?php
class Vps_Model_Select_Expr_Parent implements Vps_Model_Select_Expr_Interface
{
    private $_parent;
    private $_field;

    public function __construct($parentRule, $field)
    {
        $this->_parent = $parentRule;
        $this->_field = $field;
    }

    public function getField()
    {
        return $this->_field;
    }

    public function getParent()
    {
        return $this->_parent;
    }

    public function validate()
    {
        if (!$this->_field) {
            throw new Vps_Exception("No Field set for '"+get_class($this)+"'");
        }
        if (!is_string($this->_field)) {
            throw new Vps_Exception("Field must be of type string in '"+get_class($this)+"'");
        }
        if (!$this->_parent) {
            throw new Vps_Exception("No parent rule set for '"+get_class($this)+"'");
        }
    }

    public function getResultType()
    {
        return null;
    }

    public function toArray()
    {
        $field = $this->_field;
        if ($field instanceof Vps_Model_Select_Expr_Interface) $field = $field->toArray();
        return array(
            'exprType' => str_replace('Vps_Model_Select_Expr_', '', get_class($this)),
            'parent' => $this->_parent,
            'field' => $field,
        );
    }

    public static function fromArray(array $data)
    {
        $cls = 'Vps_Model_Select_Expr_'.$data['exprType'];
        $field = $data['field'];
        if (is_array($field)) {
            $field = Vps_Model_Select_Expr::fromArray($field);
        }
        return new $cls($data['parent'], $field);
    }
}
