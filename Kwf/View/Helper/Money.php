<?php
class Kwf_View_Helper_Money
{
    //TODO: währung übergebbar machen wenns mal benötigt wird
    public function money($amount)
    {
        $ret = number_format($amount, 2, trlcKwf('decimal separator', "."), trlcKwf('thousands separator', ","));
        $format = Kwf_Registry::get('config')->moneyFormat;
        return str_replace('{0}', $ret, $format);
    }
}
