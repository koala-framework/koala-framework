<?php
class Kwc_Calendar_Directory_CalendarView_Partial extends Kwf_Component_Partial_Abstract
{
    public function getIds()
    {
        $ret = parent::getIds();
        $beforeCount = array(
            'Mon' => 0,
            'Tue' => 1,
            'Wed' => 2,
            'Thu' => 3,
            'Fri' => 4,
            'Sat' => 5,
            'Sun' => 6,
        );
        $month = date('m');
        $year = date('Y');
        if (isset($_REQUEST['date'])) {
            $date = $_REQUEST['date'];
            $dateArray = explode('-', $date);
            if (count($dateArray) == 2 && strlen($dateArray[0]) == 2 && strlen($dateArray[1]) == 4 
                && $dateArray[0] >= 1 && $dateArray[0] <= 12) {
                $month = $dateArray[0];
                $year = $dateArray[1];
            }
        }
        $currentTimestamp = strtotime(date('d.'.$month.'.'.$year));
        $curMonthLength = date('t');
        $startDay = date('D', strtotime('01.'.$month.'.'.$year));
        $monthBefore = $month-1;
        $yearBefore = $year;
        if ($monthBefore < 1){
            $monthBefore = '12';
            $yearBefore = $year-1;
        }
        else if ($monthBefore < 10) {
            $monthBefore = '0'.$monthBefore;
        }
        $monthBeforeLength = date('t', strtotime($yearBefore.'-'.$monthBefore.'-01'));
        for ($i=$monthBeforeLength-$beforeCount[$startDay]+1; $i<=$monthBeforeLength; $i++) {
            $ret[] = 'b'.$yearBefore.$monthBefore.$i;
        }
        for ($i=1; $i<=$curMonthLength; $i++) {
            $tempDay = $i;
            if ($i < 10) {
                $tempDay = '0'.$i;
            }
            $ret[] = 'c'.date('Ym', $currentTimestamp).$tempDay;
        }
        $monthAfter = date('m', $currentTimestamp)+1;
        $yearAfter = date('Y', $currentTimestamp);
        if ($monthAfter > 12) {
            $monthAfter = '01';
            $yearAfter = date('Y', $currentTimestamp)+1;
        }
        else if ($monthAfter < 10) {
            $monthAfter = '0'.$monthAfter;
        }
        for ($i=1; $i<=(42 - ($beforeCount[$startDay] + $curMonthLength)); $i++) {
            $tempAfterDay = $i;
            if ($i < 10) {
                $tempAfterDay = '0'.$i;
            }
            $ret[] = 'a'.$yearAfter.$monthAfter.$tempAfterDay;
        }
        return $ret;
    }
}
