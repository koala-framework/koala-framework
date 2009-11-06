<?php
class Vps_Model_Select_Expr_String implements Vps_Model_Select_Expr_Interface
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
            throw new Vps_Exception("No Field-Value set for '"+get_class($this)+"'");
        }
    }

    public function getResultType()
    {
        return Vps_Model_Interface::TYPE_STRING;
    }
}
