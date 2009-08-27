<?php
class Vps_View_Helper_DateTime extends Vps_View_Helper_Date
{
    public function dateTime($date, $format = null)
    {
        if (!$format) $format = trlVps('Y-m-d H:i');
        return $this->date($date, $format);
    }
}
