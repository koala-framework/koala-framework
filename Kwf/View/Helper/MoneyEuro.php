<?php
/**
 * @deprecated
 * @see Vps_View_Helper_Money
 */
class Vps_View_Helper_MoneyEuro extends Vps_View_Helper_Money
{
    public function moneyEuro($amount)
    {
        return $this->money($amount);
    }
}
