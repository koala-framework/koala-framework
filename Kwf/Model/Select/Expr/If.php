<?php
/**
 * @package Model
 * @subpackage Expr
 */
class Kwf_Model_Select_Expr_If implements Kwf_Model_Select_Expr_Interface
{
    private $_if;
    private $_then;
    private $_else;
    public function __construct(Kwf_Model_Select_Expr_Interface $if, Kwf_Model_Select_Expr_Interface $then, Kwf_Model_Select_Expr_Interface $else)
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

    public function toArray()
    {
        return array(
            'exprType' => str_replace('Kwf_Model_Select_Expr_', '', get_class($this)),
            'if' => $this->_if->toArray(),
            'then' => $this->_then->toArray(),
            'else' => $this->_else->toArray(),
        );
    }

    public static function fromArray(array $data)
    {
        $cls = 'Kwf_Model_Select_Expr_'.$data['exprType'];
        return new $cls(Kwf_Model_Select_Expr::fromArray($data['if']),
                        Kwf_Model_Select_Expr::fromArray($data['then']),
                        Kwf_Model_Select_Expr::fromArray($data['else']));
    }
}
