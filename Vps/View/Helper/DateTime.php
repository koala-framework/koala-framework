<?php
class Vps_View_Helper_DateTime
{
    public function dateTime($date)
    {
        if (!$date) return '';
        if (is_string($date)) $date = strtotime($date);
        return date(trlVps('Y-m-d H:i'), $date);
    }
}
