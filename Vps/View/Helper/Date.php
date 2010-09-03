<?php
class Vps_View_Helper_Date
{
    public function date($date, $format = null)
    {
        if (!$format) $format = trlVps('Y-m-d');

        if (!$date || substr($date, 0, 10) == '0000-00-00') return '';

        $d = new Vps_Date($date);
        return $d->format($format);

        /*
        Das ist schneller, kann aber keine übersetzung bei Monatsnamen etc
        $datetime = new DateTime($date);
        return $datetime->format($format);
        */
    }
}
