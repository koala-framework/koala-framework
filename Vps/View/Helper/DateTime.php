<?php
class Vps_View_Helper_DateTime
{
    public function dateTime($date)
    {
        if (!$date) return '';
        $datetime = new DateTime($date);
        return $datetime->format(trlVps('Y-m-d H:i'));
    }
}
