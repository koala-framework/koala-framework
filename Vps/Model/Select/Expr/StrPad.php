<?php
class Vps_Model_Select_Expr_StrPad implements Vps_Model_Select_Expr_Interface
{
    const LEFT = 'left';
    const RIGHT = 'right';

    private $_field;
    private $_padLength;
    private $_padStr;
    private $_padType;

    public function __construct($field, $padLength, $padStr = ' ', $padType = Vps_Model_Select_Expr_StrPad::RIGHT)
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
            throw new Vps_Exception("No Field set for '"+get_class($this)+"'");
        }
        if (!$this->_padLength) {
            throw new Vps_Exception("No padLength set for '"+get_class($this)+"'");
        }
    }

    public function getResultType()
    {
        return Vps_Model_Interface::TYPE_STRING;
    }
}