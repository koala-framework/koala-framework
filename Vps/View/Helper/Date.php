<?php
class Vps_View_Helper_Date
{
    public function date($date)
    {
        if (!$date) return '';
        if (is_string($date)) $date = strtotime($date);
        return date(trlVps('Y-m-d'), $date);
    }
}
