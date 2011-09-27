<?php
class Vps_Model_Select_Expr_If implements Vps_Model_Select_Expr_Interface
{
    private $_if;
    private $_then;
    private $_else;
    public function __construct(Vps_Model_Select_Expr_Interface $if, Vps_Model_Select_Expr_Interface $then, Vps_Model_Select_Expr_Interface $else)
    {
        $this->_if = $if;
        $this->_then = $then;
        $this->_else = $else;
    }

    public function getIf()
    {
        return $this->_if;
    }

    public function getThen()
    {
        return $this->_then;
    }

    public function getElse()
    {
        return $this->_else;
    }

    public function validate()
    {
        $this->_if->validate();
        $this->_then->validate();
        $this->_else->validate();
    }

    public function getResultType()
    {
        if ($this->_then->getResultType() == $this->_else->getResultType()) {
            return $this->_then->getResultType();
        }
        return null;
    }
}
