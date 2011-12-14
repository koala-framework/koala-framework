<?php
/**
 * @package Model
 * @subpackage Expr
 * @deprecated
 * @internal
 *
 * Ist nur dafür da damit der Service noch mit Webs die HigherDate verwenden funktioniert (<1.11)
 */
class Kwf_Model_Select_Expr_HigherDate extends Kwf_Model_Select_Expr_Higher
{
    public function __construct($field, $value)
    {
        throw new Kwf_Exception("deprecated, use Higher with Kwf_Date(Time)");
    }

    public function __wakeup()
    {
        if (is_string($this->_value)) {
            if (strlen($this->_value) > 11) {
                $this->_value = new Kwf_DateTime(strtotime($this->_value));
            } else {
                $this->_value = new Kwf_Date(strtotime($this->_value));
            }
        }
    }
}