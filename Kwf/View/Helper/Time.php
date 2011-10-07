<?php
class Kwf_View_Helper_Time
{
    public function time($time, $format = null)
    {
        if (!$format) $format = trlKwf('H:i');
        if (!$time) return '';
        if (is_string($time)) $time = strtotime($time);
        return date($format, $time);
    }
}
