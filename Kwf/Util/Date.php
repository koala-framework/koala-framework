<?php
class Vps_Util_Date
{
    public static function dateForCalendarWeek($week, $year = null)
    {
        if (!$year) $year = date('Y');
        $i = mktime(0,0,0,1,1,$year);

        while (date('W', $i) != 1) { //KW 1 suchen
            $i += 24*60*60;
        }
        $i += 7*24*60*60*($week-1);
        if (date('Y', $i) != $year) return 0;
        return $i;
    }
}
