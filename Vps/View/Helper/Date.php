<?php
class Vps_View_Helper_Date
{
    public function date($date, $format = null)
    {
        if (!$format) $format = trlVps('Y-m-d');

        if (!$date) return '';
return date($format, strtotime($date));
        $d = new Vps_Date($date);
        return $d->toString($format);

        /*
        Das ist schneller, kann aber keine übersetzung bei Monatsnamen etc
        $datetime = new DateTime($date);
        return $datetime->format($format);
        */
    }
}
