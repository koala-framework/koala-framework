<?php
class Vps_View_Helper_Money
{
    public function money($amount, $valuta = ' €')
    {
        if (!$valuta) $valuta = '';
        $ret = number_format($amount, 2, ",", ".").$valuta;
        return $ret;
    }
}
