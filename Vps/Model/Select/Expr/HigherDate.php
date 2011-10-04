<?php
/**
 * @deprecated
 *
 * Ist nur dafür da damit der Service noch mit Webs die HigherDate verwenden funktioniert (<1.11)
 */
class Vps_Model_Select_Expr_HigherDate extends Vps_Model_Select_Expr_Higher
{
    public function __construct($field, $value)
    {
        throw new Vps_Exception("deprecated, use Higher with Vps_Date(Time)");
    }

    public function __wakeup()
    {
        if (is_string($this->_value)) {
            if (strlen($this->_value) > 11) {
                $this->_value = new Vps_DateTime(strtotime($this->_value));
            } else {
                $this->_value = new Vps_Date(strtotime($this->_value));
            }
        }
    }
}