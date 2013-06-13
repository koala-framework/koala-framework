<?php
class Kwc_Calendar_Directory_CalendarView_Component extends Kwc_Directories_List_View_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['partialClass'] = 'Kwc_Calendar_Directory_CalendarView_Partial';
        $ret['generators']['child']['component']['paging'] = 'Kwc_Calendar_Directory_CalendarView_Paging_Component';
        return $ret;
    }

    public function getPartialVars($partial, $nr, $info)
    {
        $ret = $info;
        $ret['item'] = null;
        $ret['number'] = substr($nr, -2);
        $date = new Kwf_Date(substr($nr, 1, 4).'-'.substr($nr, 5, 2).'-'.substr($nr, -2));
        $type = substr($nr, 0, 1);
        $s = new Kwf_Component_Select();
        $s->where(new Kwf_Model_Select_Expr_And(array(
            new Kwf_Model_Select_Expr_HigherEqual('from',$date->format('Y-m-d').' 00:00:00'),
            new Kwf_Model_Select_Expr_LowerEqual('from', $date->format('Y-m-d').' 23:59:59')
        )));
        $s->whereGenerator('detail');
        $event = $this->getData()->parent->getChildComponent($s);
        $ret['cssClass'] = 'day';
        $ret['clear'] = false;
        if (($info['number']+1)%7 == 0) {
            $ret['clear'] = true;
        }
        if ($type == 'b') {
            $ret['cssClass'] .= ' before';
        } else if ($type == 'a') {
            $ret['cssClass'] .= ' after';
        }
        if ($event) {
            $ret['item'] = $event;
            $ret['cssClass'] .= ' hasEvent';
        }
        return $ret;
    }
}
