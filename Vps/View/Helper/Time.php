<?php
class Vps_View_Helper_Time
{
    public function time($time)
    {
        if (!$time) return '';
        if (is_string($time)) $time = strtotime($time);
        return date(trlVps('H:i'), $time);
    }
}
