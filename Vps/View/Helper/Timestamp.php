<?php
class Vps_View_Helper_Timestamp
{
    public function timestamp($date)
    {
        $timeHelper = new Vps_View_Helper_Time();
        $time = $timeHelper->time($date);
        $dateHelper = new Vps_View_Helper_Date();
        $date = $dateHelper->date($date);
        return trlcVps('time', 'On') . ' ' . $date . ' ' . trlcVps('time', 'at') . ' ' . $time;
    }
}
