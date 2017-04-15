<?php
class Kwc_Calendar_Directory_CalendarView_Paging_Component extends Kwc_Abstract
    implements Kwf_Component_Partial_Interface
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['partialClass'] = 'Kwc_Calendar_Directory_CalendarView_Partial';
        return $ret;
    }

    public static function getPartialClass($componentClass)
    {
        return 'Kwc_Calendar_Directory_CalendarView_Paging_Partial';
    }

    public function getPartialVars($partial, $nr, $info)
    {
        $ret['targetUrl'] = $this->getData()->parent->parent->getUrl();

        $month = substr($nr, 0, 2);
        $year = substr($nr, -4);
        $locale = new Zend_Locale($this->getData()->getLanguage());
        $date = new Zend_Date(strtotime($year.'-'.$month.'-01'), false, $locale);
        $ret['currentMonth'] = $date->toString(Zend_Date::MONTH_NAME).' '.$year;
        $monthBefore = $month-1;
        $yearBefore = $year;
        if ($monthBefore < 1){
            $monthBefore = '12';
            $yearBefore = $year-1;
        }
        else if ($monthBefore < 10) {
            $monthBefore = '0'.$monthBefore;
        }
        $ret['back'] = $monthBefore.'-'.$yearBefore;
        $monthAfter = $month+1;
        $yearAfter = $year;
        if ($monthAfter > 12) {
            $monthAfter = '01';
            $yearAfter = $year+1;
        }
        else if ($monthAfter < 10) {
            $monthAfter = '0'.$monthAfter;
        }
        $ret['next'] = $monthAfter.'-'.$yearAfter;
        return $ret;
    }
    public function getPartialParams()
    {
        return array();
    }

}
