<?php
/**
 * @deprecated
 *
 * Ist nur dafür da damit der Service noch mit Webs die LowerDate verwenden funktioniert (<1.11)
 */
class Vps_Model_Select_Expr_SmallerDate extends Vps_Model_Select_Expr_Lower
{
    public function __construct($field, $value)
    {
        throw new Vps_Exception("deprecated, use Lower with Vps_Date(Time)");
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