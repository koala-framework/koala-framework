<?php
class Vps_View_Helper_Date
{
    public function date($date)
    {
        if (!$date) return '';
        $datetime = new DateTime($date);
        return $datetime->format(trlVps('Y-m-d'));
    }
}
