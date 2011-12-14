<?php
/**
 * @package Model
 * @subpackage Expr
 */
class Kwf_Model_Select_Expr_String implements Kwf_Model_Select_Expr_Interface
{
    protected $_string;

    public function __construct($string) {
        $this->_string = $string;
    }

    public function getString()
    {
        return $this->_string;
    }

    public function validate()
    {
        if (!$this->_string) {
            throw new Kwf_Exception("No Field-Value set for '"+get_class($this)+"'");
        }
    }

    public function getResultType()
    {
        return Kwf_Model_Interface::TYPE_STRING;
    }


    public function toArray()
    {
        return array(
            'exprType' => str_replace('Kwf_Model_Select_Expr_', '', get_class($this)),
            'string' => $this->_string,
        );
    }

    public static function fromArray(array $data)
    {
        $cls = 'Kwf_Model_Select_Expr_'.$data['exprType'];
        return new $cls($data['string']);
    }
}
