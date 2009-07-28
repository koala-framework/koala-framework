<?php
class Vps_View_Helper_Money
{
    //TODO: währung übergebbar machen wenns mal benötigt wird
    public function money($amount)
    {
                                          //TODO: übersetzung für , und .
        $ret = number_format($amount, 2, ",", ".");
        $format = Vps_Registry::get('config')->moneyFormat;
        return str_replace('{0}', $ret, $format);
    }
}
