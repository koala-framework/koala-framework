<?php
class Vps_View_Helper_Time
{
    public function time($time, $format = null)
    {
        if (!$format) $format = trlVps('H:i');
        if (!$time) return '';
        if (is_string($time)) $time = strtotime($time);
        return date($format, $time);
    }
}
