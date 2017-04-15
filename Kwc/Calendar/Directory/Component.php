<?php
class Kwc_Calendar_Directory_Component extends Kwc_Directories_ItemPage_Directory_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['componentName'] = 'Calendar';
        $ret['cssClass'] = 'webStandard';
//         $ret['viewCache'] = false;
        $ret['generators']['child']['component']['view'] = 'Kwc_Calendar_Directory_CalendarView_Component';
        $ret['generators']['detail']['component'] = 'Kwc_Calendar_Detail_Component';
        $ret['extConfig'] = 'Kwc_Directories_Item_Directory_ExtConfigTabs';
        $ret['childModel'] = 'Kwc_Calendar_Directory_Model';
        return $ret;
    }
    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $translation = array(
            'month' => array(
                '01' => 'Jänner',
                '02' => 'Februar',
                '03' => 'März',
                '04' => 'April',
                '05' => 'Mai',
                '06' => 'Juni',
                '07' => 'Juli',
                '08' => 'August',
                '09' => 'September',
                '10' => 'Oktober',
                '11' => 'November',
                '12' => 'Dezember'
            )
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
        $allEvents = array();
        $linkHelper = new Kwf_Component_View_Helper_ComponentLink();
        $s = new Kwf_Component_Select();
        $s->whereGenerator('detail');
        foreach ($this->getData()->getChildComponents($s) as $child) {
            $date = explode(' ', $child->row->from);
            $date = $date[0];
            $allEvents[$date] = $child;
        }
        $beforeCount = array(
            'Mon' => 0,
            'Tue' => 1,
            'Wed' => 2,
            'Thu' => 3,
            'Fri' => 4,
            'Sat' => 5,
            'Sun' => 6,
        );
        $ret['config'] = array();
        //get current month and day
        $startDay = date('D', strtotime('01.'.$month.'.'.$year));
        $daysBefore = array();
        $monthBefore = $month-1;
        $yearBefore = $year;
        if ($monthBefore < 1){
            $monthBefore = '12';
            $yearBefore = $year-1;
        }
        else if ($monthBefore < 10) {
            $monthBefore = '0'.$monthBefore;
        }
        if ($beforeCount[$startDay]) {
            $lastBefore = date('t', strtotime('01.'.$monthBefore.'.'.$yearBefore));
            $start = $lastBefore - $beforeCount[$startDay] +1;
            for ($i=$start; $i<=$lastBefore; $i++){
                if ($i < 10) {
                    $day = '0'.$i;
                } else {
                    $day = (string)$i;
                }
                $event = false;
                $timestamp = strtotime($day.'.'.$monthBefore.'.'.$yearBefore);
                $class = 'before';
                if (array_key_exists($yearBefore.'-'.$monthBefore.'-'.$day, $allEvents)) {
                    $event = $allEvents[$yearBefore.'-'.$monthBefore.'-'.$day];
                    $class .= ' hasEvent';
                }
                $dayNumber = $i;
                if ($event) {
                    $dayNumber = $linkHelper->componentLink($event, $i);
                }
                $daysBefore[] = array(
                    'date' => date('d.m.Y', $timestamp),
                    'dayNumber' => $dayNumber,
                    'dayText' => date('l', $timestamp),
                    'class' => $class,
                    'event' => $event
                );
            }
        }
        $daysCurrent = array();
        for ($i=1; $i<=date('t', $currentTimestamp); $i++){
            if ($i < 10) {
                $day = '0'.$i;
            } else {
                $day = (string)$i;
            }
            $event = false;
            $class = 'current';
            if (array_key_exists(date('Y-m-', $currentTimestamp).$day, $allEvents)) {
                $event = $allEvents[date('Y-m-', $currentTimestamp).$day];
                $class .= ' hasEvent';
            }
            $dayNumber = $i;
            if ($event) {
                $dayNumber = $linkHelper->componentLink($event, $i);
            }
            $daysCurrent[] = array(
                'date' => date('d.m.Y', $currentTimestamp),
                'dayNumber' => $dayNumber,
                'dayText' => date('l', $currentTimestamp),
                'class' => $class,
                'event' => $event
            );
        }
        $countAfter = 42 - (count($daysBefore) + count($daysCurrent));
        $daysAfter = array();
        $monthAfter = date('m', $currentTimestamp)+1;
        $yearAfter = date('Y', $currentTimestamp);
        if ($monthAfter > 12) {
            $monthAfter = '01';
            $yearAfter = date('Y', $currentTimestamp)+1;
        }
        else if ($monthAfter < 10) {
            $monthAfter = '0'.$monthAfter;
        }
        if ($countAfter) {
            for ($i=1; $i<=$countAfter; $i++) {
                if ($i < 10) {
                    $day = '0'.$i;
                } else {
                    $day = (string)$i;
                }
                $event = false;
                $timestamp = strtotime($day.'.'.$monthAfter.'.'.$yearAfter);
                $class = 'after';
                if (array_key_exists($yearAfter.'-'.$monthAfter.'-'.$day, $allEvents)) {
                    $event = $allEvents[$yearAfter.'-'.$monthAfter.'-'.$day];
                    $class .= ' hasEvent';
                }
                $dayNumber = $i;
                if ($event) {
                    $dayNumber = $linkHelper->componentLink($event, $i);
                }
                $daysAfter[] = array(
                'date' => date('d.m.Y', $timestamp),
                'dayNumber' => $dayNumber,
                'dayText' => date('l', $timestamp),
                'class' => $class,
                'event' => $event
            );
            }
        }
        $ret['days'] = array();
        $i = 1;
        foreach (array_merge($daysBefore, $daysCurrent, $daysAfter) as $day) {
            if ($i == 1) {
                $day['class'] .= ' first';
            } else if ($i == 42) {
                $day['class'] .= ' last';
            } else if ($i % 7 == 0) {
                $day['class'] .= ' lastInRow';
            } else if (($i-1) % 7 == 0) {
                $day['class'] .= ' firstInRow';
            }
            if ($i <= 7) {
                $day['class'] .= ' firstRow';
            }
            $ret['days'][] = $day;
            $i++;
        }
        $ret['config']['controllerUrl'] = Kwc_Admin::getInstance($this->getData()
            ->componentClass)->getControllerUrl('Component');
        $ret['back'] = $monthBefore.'-'.$yearBefore;
        $ret['next'] = $monthAfter.'-'.$yearAfter;
        $currentMonth = date('m', $currentTimestamp);
        $currentMonth = $translation['month'][$currentMonth];
        $currentYear = date('Y', $currentTimestamp);
        $ret['currentMonth'] = $currentMonth.' - '.$currentYear;
        return $ret;
    }
}
