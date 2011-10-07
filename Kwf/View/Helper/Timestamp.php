<?php
/**
 * @deprecated
 */
class Kwf_View_Helper_Timestamp
{
    public function timestamp($date)
    {
        if (!$date) return '-';
        $timeHelper = new Kwf_View_Helper_Time();
        $time = $timeHelper->time($date);
        $dateHelper = new Kwf_View_Helper_Date();
        $date = $dateHelper->date($date);
        return trlcKwf('time', 'On') . ' ' . $date . ' ' . trlcKwf('time', 'at') . ' ' . $time;
    }
}
