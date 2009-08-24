<?php
class Vps_View_Helper_Date
{
    public function date($date)
    {
        if (!$date) return '';
        $date = new Zend_Date($date);
        return $date->toString(trlVps('Y-m-d'));
    }
}
