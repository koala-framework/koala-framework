<?php
class Kwf_View_Helper_SecondsAsDuration
{
    public function secondsAsDuration($seconds)
    {
        $hours   = floor($seconds / 3600);
        $minutes = floor(($seconds % 3600) / 60);
        $seconds = ($seconds % 3600 % 60);

        return sprintf('%02d:%02d:%02d', $hours, $minutes, $seconds);
    }
}
