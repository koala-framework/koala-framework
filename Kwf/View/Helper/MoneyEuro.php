<?php
/**
 * @deprecated
 * @see Kwf_View_Helper_Money
 */
class Kwf_View_Helper_MoneyEuro extends Kwf_View_Helper_Money
{
    public function moneyEuro($amount)
    {
        return $this->money($amount);
    }
}
