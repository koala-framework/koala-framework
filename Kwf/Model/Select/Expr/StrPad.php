<?php
class Kwf_Model_Select_Expr_StrPad implements Kwf_Model_Select_Expr_Interface
{
    const LEFT = 'left';
    const RIGHT = 'right';

    private $_field;
    private $_padLength;
    private $_padStr;
    private $_padType;

    public function __construct($field, $padLength, $padStr = ' ', $padType = Kwf_Model_Select_Expr_StrPad::RIGHT)
    {
        $this->_field = $field;
        $this->_padLength = $padLength;
        $this->_padStr = $padStr;
        $this->_padType = $padType;
    }

    public function getField()
    {
        return $this->_field;
    }

    public function getPadLength()
    {
        return $this->_padLength;
    }

    public function getPadStr()
    {
        return $this->_padStr;
    }

    public function getPadType()
    {
        return $this->_padType;
    }

    public function validate()
    {
        if (!$this->_field) {
            throw new Kwf_Exception("No Field set for '"+get_class($this)+"'");
        }
        if (!$this->_padLength) {
            throw new Kwf_Exception("No padLength set for '"+get_class($this)+"'");
        }
    }

    public function getResultType()
    {
        return Kwf_Model_Interface::TYPE_STRING;
    }

    public function toArray()
    {
        $field = $this->_field;
        if ($field instanceof Vps_Model_Select_Expr_Interface) $field = $field->toArray();
        return array(
            'exprType' => str_replace('Vps_Model_Select_Expr_', '', get_class($this)),
            'field' => $field,
            'padLength' => $this->_padLength,
            'padStr' => $this->_padStr,
            'padType' => $this->_padType,
        );
    }

    public static function fromArray(array $data)
    {
        $cls = 'Vps_Model_Select_Expr_'.$data['exprType'];
        $field = $data['field'];
        if (is_array($field)) {
            $field = Vps_Model_Select_Expr::fromArray($field);
        }
        return new $cls($field, $data['padLength'], $data['padStr'], $data['padType']);
    }
}