<?php
class Vps_View_Helper_Money
{
    //TODO: währung übergebbar machen wenns mal benötigt wird
    public function money($amount)
    {
        $ret = number_format($amount, 2, trlcVps('decimal separator', "."), trlcVps('thousands separator', ","));
        $format = Vps_Registry::get('config')->moneyFormat;
        return str_replace('{0}', $ret, $format);
    }
}
