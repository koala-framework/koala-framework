<?php
class Vps_View_Helper_MoneyEuro
{
    public function moneyEuro($amount)
    {
        $ret = number_format($amount, 2, ",", ".");
        $ret .= ' €';
        return $ret;
    }
}
