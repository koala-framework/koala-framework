<?php
class Kwc_Calendar_Directory_CalendarView_Paging_Partial extends Kwf_Component_Partial_Abstract
{
    public function getIds()
    {
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
        return array($month.$year);
    }
}